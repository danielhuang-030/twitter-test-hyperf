<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use Carbon\Carbon;
use Hyperf\HttpServer\Request;

class JiraController extends AbstractController
{
    /**
     * project keys
     *
     * @var array
     */
    const PROJECT_KEYS = [
        'TPRD',
        'TPRD2',
    ];

    /**
     * index
     */
    public function index(Request $request)
    {
        $apiCaller = new JiraAPICaller(
            new BasicAuthAPICaller(
                config('jira_api_url'),
                config('jira_api_user'),
                config('jira_api_token')
            )
        );

        $dateRange = $request->input('date', Carbon::now()->format('Y-m-d'));
        $accountId = $request->input('aid', '6090b024afcdb70069ff8bbf');
        $worklogs = $apiCaller->worklogs(explode(',', $dateRange), $accountId);

        return [
            'worklogs' => formatWorklogsV2($worklogs),
            'origin_data' => $worklogs,
        ];
    }

    /**
     * info
     *
     * @param Request $request
     *
     * @return void
     */
    public function info(Request $request)
    {
        // init
        $apiCaller = new JiraAPICaller(
            new BasicAuthAPICaller(
                config('jira_api_url'),
                config('jira_api_user'),
                config('jira_api_token')
            )
        );
        $jiraAgile = new JiraAgile(
            new BasicAuthAPICaller(
                config('jira_agile_url'),
                config('jira_api_user'),
                config('jira_api_token')
            )
        );

        // 取得指定看板資訊
        $boards = $jiraAgile->getBoards(static::PROJECT_KEYS);

        // 取得 sprints
        $sprints = $jiraAgile->getBoardSprints((int) data_get(current($boards), 'id'));

        // 取得可指定的使用者列表
        $users = $spent = [];
        foreach ($boards as $board) {
            $projectKey = data_get($board, 'key');
            $users[$projectKey] = $apiCaller->getAssignableUsers($projectKey);
        }

        return [
            'boards' => $boards,
            'sprints' => $sprints,
            'users' => $users,
        ];
    }

    /**
     * spent
     *
     * @param Request $request
     *
     * @return void
     */
    public function spent(Request $request)
    {
        $dateRange = explode(',', $request->input('range'));
        if (empty($dateRange)) {
            $dateStart = date('Y/m/d');
            $dateEnd = date('Y/m/d');
        } else if (2 != count($dateRange)) {
            $date = current($dateRange);
            $dateStart = Carbon::parse($date)->format('Y/m/d');
            $dateEnd = Carbon::parse($date)->format('Y/m/d');
        } else {
            list($dateStart, $dateEnd) = $dateRange;
            $dateStart = Carbon::parse($dateStart)->format('Y/m/d');
            $dateEnd = Carbon::parse($dateEnd)->format('Y/m/d');
        }

        // init
        $apiCaller = new JiraAPICaller(
            new BasicAuthAPICaller(
                config('jira_api_url'),
                config('jira_api_user'),
                config('jira_api_token')
            )
        );
        $jiraAgile = new JiraAgile(
            new BasicAuthAPICaller(
                config('jira_agile_url'),
                config('jira_api_user'),
                config('jira_api_token')
            )
        );

        // 取得指定看板資訊
        $boards = $jiraAgile->getBoards(static::PROJECT_KEYS);

        // 取得可指定的使用者列表
        $users = $spent = [];
        foreach ($boards as $board) {
            $projectKey = data_get($board, 'key');
            $users[$projectKey] = $apiCaller->getAssignableUsers($projectKey);

            $now = Carbon::now();
            foreach ($users[$projectKey] as $user) {
                $accountId = data_get($user, 'id');

                // spent
                $spent[$accountId] = [
                    'name' => data_get($user, 'name'),
                    'spent_sec' => $spentSec = $apiCaller->getWorklogTotalSpentTime([
                        $dateStart,
                        $dateEnd,
                    ], $accountId),
                    'spent' => $now->diffInHours($now->copy()->addSeconds($spentSec)),
                ];
            }
        }

        return [
            'spent' => $spent,
        ];
    }
}

class BasicAuthAPICaller
{
    protected $baseUrl;

    protected $basicAuth;

    public function __construct(
        string $baseUrl,
        string $user,
        string $token
    ) {
        $this->baseUrl = $baseUrl;
        $this->basicAuth = base64_encode(implode(':', [
            $user,
            $token,
        ]));
    }

    public function get(string $uri, array $query = [])
    {
        return $this->call('GET', $uri, $query);
    }

    public function post(string $uri, array $postdata = [], array $query = [])
    {
        return $this->call('POST', $uri, $query, $postdata);
    }

    protected function call(string $method, string $uri, array $query = [], array $data = [])
    {
        return json_decode(file_get_contents("{$this->baseUrl}{$uri}?" . http_build_query($query), false, stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", [
                    'Accept: application/json',
                    'Content-Type: application/json',
                    "Authorization: Basic {$this->basicAuth}",
                ]),
                'content' => ($data ? json_encode($data) : ''),
            ],
        ])));
    }
}

/**
 * JiraAPICaller
 */
class JiraAPICaller
{
    /**
     * BasicAuthAPICaller
     *
     * @var BasicAuthAPICaller
     */
    protected $aBasicAuthAPICaller;

    public function __construct(
        BasicAuthAPICaller $aBasicAuthAPICaller
    ) {
        $this->aBasicAuthAPICaller = $aBasicAuthAPICaller;
    }

    /**
     * worklogs.
     */
    public function worklogs(array $dateRange = [], string $author = 'currentuser()'): array
    {
        $worklogResult = [];

        if (empty($dateRange)) {
            $dateStart = date('Y/m/d');
            $dateEnd = date('Y/m/d');
        } else if (2 != count($dateRange)) {
            $date = current($dateRange);
            $dateStart = Carbon::parse($date)->format('Y/m/d');
            $dateEnd = Carbon::parse($date)->format('Y/m/d');
        } else {
            list($dateStart, $dateEnd) = $dateRange;
            $dateStart = Carbon::parse($dateStart)->format('Y/m/d');
            $dateEnd = Carbon::parse($dateEnd)->format('Y/m/d');
        }

        $jql = implode(' AND ', [
            sprintf('worklogDate>="%s"', $dateStart),
            sprintf('worklogDate<="%s"', $dateEnd),
            sprintf('worklogAuthor=%s', $author),
        ]);
        $searchResult = $this->aBasicAuthAPICaller->get('/search', [
            'jql' => $jql,
            'fields' => 'summary,parent,key,issuetype,customfield_10014,worklog',
        ]);
        if (empty($searchResult->issues)) {
            return $worklogResult;
        }
        foreach ($searchResult->issues as $issue) {
            // 取得 epic
            $epic = '';
            if (! empty($issue->fields->customfield_10014)) {
                $epicResult = $this->aBasicAuthAPICaller->get(sprintf('/issue/%s', $issue->fields->customfield_10014));
                $epic = $epicResult->fields->summary ?? '';
            }

            // worklogs
            if (empty($issue->fields->worklog->worklogs)) {
                continue;
            }
            foreach ($issue->fields->worklog->worklogs as $worklog) {
                // 日期過濾
                if (empty($worklog->started)) {
                    continue;
                }
                $worklogStartedAt = Carbon::parse($worklog->started)->format('Y/m/d');
                if ($dateStart > $worklogStartedAt || $dateEnd < $worklogStartedAt) {
                    continue;
                }

                // $worklogResult[$epic][$issue->key]['summary'] = preg_replace('#\[.+\]\s*#i', '', $issue->fields->summary ?? '');
                $worklogResult[$epic][$issue->key]['summary'] = $issue->fields->summary ?? '';
                $worklogResult[$epic][$issue->key]['worklogs'][] = [
                    'created' => date('Y-m-d H:i:s', strtotime(str_replace('+0800', '', $worklog->created))),
                    'started' => date('Y-m-d H:i:s', strtotime(str_replace('+0800', '', $worklog->started))),
                    'spent' => $worklog->timeSpent,
                    'spent_sec' => $worklog->timeSpentSeconds,
                    'contents' => !empty($worklog->comment->content) ? formatWorklogContent($worklog->comment->content) : '',
                ];
                if (data_get($issue, 'fields.issuetype.subtask', false)) {
                    $worklogResult[$epic][$issue->key]['parent'] = data_get($issue, 'fields.parent.key', '');
                }
            }
        }

        return $worklogResult;
    }

    /**
     * get assignable users
     *
     * @param string $project
     *
     * @return array
     */
    public function getAssignableUsers(string $project): array
    {
        $result = [];
        $users = $this->aBasicAuthAPICaller->get('/user/search/query', [
            'query' => sprintf('is assignee of (%s)', $project),
        ]);
        if (empty($users->values)) {
            return $result;
        }
        foreach ($users->values as $user) {
            $result[$user->accountId] = [
                'id' => $user->accountId,
                'name' => $user->displayName,
            ];
        }

        return $result;
    }

    /**
     * get worklog total spent time
     */
    public function getWorklogTotalSpentTime(array $dateRange = [], string $author = 'currentuser()'): int
    {
        $total = 0;

        if (empty($dateRange)) {
            $dateStart = date('Y/m/d');
            $dateEnd = date('Y/m/d');
        } else if (2 != count($dateRange)) {
            $date = current($dateRange);
            $dateStart = Carbon::parse($date)->format('Y/m/d');
            $dateEnd = Carbon::parse($date)->format('Y/m/d');
        } else {
            list($dateStart, $dateEnd) = $dateRange;
            $dateStart = Carbon::parse($dateStart)->format('Y/m/d');
            $dateEnd = Carbon::parse($dateEnd)->format('Y/m/d');
        }

        $jql = implode(' AND ', [
            sprintf('worklogDate>="%s"', $dateStart),
            sprintf('worklogDate<="%s"', $dateEnd),
            sprintf('worklogAuthor=%s', $author),
        ]);
        $searchResult = $this->aBasicAuthAPICaller->get('/search', [
            'jql' => $jql,
            'fields' => 'summary,key,worklog',
        ]);
        if (empty($searchResult->issues)) {
            return $total;
        }
        foreach ($searchResult->issues as $issue) {
            // worklogs
            if (empty($issue->fields->worklog->worklogs)) {
                continue;
            }
            foreach ($issue->fields->worklog->worklogs as $worklog) {
                // 日期過濾
                if (empty($worklog->started)) {
                    continue;
                }
                $worklogStartedAt = Carbon::parse($worklog->started)->format('Y/m/d');
                if ($dateStart > $worklogStartedAt || $dateEnd < $worklogStartedAt) {
                    continue;
                }

                $total += $worklog->timeSpentSeconds;
            }
        }

        return $total;
    }
}

/**
 * Jira Agile
 */
class JiraAgile
{
    /**
     * BasicAuthAPICaller
     *
     * @var BasicAuthAPICaller
     */
    protected $basicAuth;

    /**
     * construct
     */
    public function __construct(
        BasicAuthAPICaller $basicAuthAPICaller
    ) {
        $this->basicAuth = $basicAuthAPICaller;
    }

    /**
     * get boards
     *
     * @link https://developer.atlassian.com/cloud/jira/software/rest/api-group-board/#api-agile-1-0-board-boardid-get
     *
     * @param array $projectKeys
     *
     * @return array
     */
    public function getBoards(array $projectKeys): array
    {
        $result = [];
        $boards = $this->basicAuth->get('/board');
        if (empty($boards->values)) {
            return $result;
        }
        foreach ($boards->values as $board) {
            if (!in_array($board->location->projectKey, $projectKeys)) {
                continue;
            }
            $result[$board->id] = [
                'id' => $board->id,
                'key' => $board->location->projectKey,
                'name' => $board->location->displayName,
            ];
        }

        return $result;
    }

    /**
     * get board sprints
     *
     * @link https://developer.atlassian.com/cloud/jira/software/rest/api-group-board/#api-agile-1-0-board-boardid-sprint-get
     *
     * @param int $boardId
     *
     * @return array
     */
    public function getBoardSprints(int $boardId): array
    {
        $result = [];
        $sprints = $this->basicAuth->get(sprintf('/board/%d/sprint', $boardId), [
            'state' => 'active,closed',
        ]);
        if (empty($sprints->values)) {
            return $result;
        }
        foreach ($sprints->values as $sprint) {
            $result[$sprint->id] = [
                'id' => $sprint->id,
                'name' => $sprint->name,
                'state' => $sprint->state,
                'started_at' => Carbon::parse($sprint->startDate)->format('Y-m-d H:i:s'),
                'ended_at' => Carbon::parse($sprint->endDate)->format('Y-m-d H:i:s'),
            ];
        }
        $startedAt = Carbon::now()->subMonths(1);

        return collect($result)->filter(function ($sprint) use ($startedAt) {
            return Carbon::parse($sprint['started_at'])->greaterThan($startedAt);
        })->sortBy('started_at')->all();
    }
}

/**
 * format worklog content.
 */
function formatWorklogContent(array $contents): array
{
    $result = [];
    foreach ($contents as $content) {
        switch ($content->type) {
            case 'text':
                $result[] = $content->text;
                break;
            default:
                if (is_array($content->content)) {
                    $result = array_merge($result, formatWorklogContent($content->content));
                }
                break;
        }
    }

    return $result;
}

/**
 * format worklogs.
 */
function formatWorklogs(array $worklogs): string
{
    $result = '';
    if (empty($worklogs)) {
        return $result;
    }
    foreach ($worklogs as $epic => $issues) {
        $result .= sprintf("[%s]\n", $epic);
        foreach ($issues as $issueKey => $issue) {
            $result .= sprintf("- [%s] %s\n", $issueKey, $issue['summary']);
            foreach ($issue['worklogs'] as $worklog) {
                foreach ($worklog['contents'] as $content) {
                    $result .= sprintf("  - %s\n", $content);
                }
            }
        }
        $result .= sprintf("\n");
    }

    return $result;
}

/**
 * format worklogs.
 */
function formatWorklogsV2(array $worklogs): array
{
    $result = [];
    if (empty($worklogs)) {
        return $result;
    }
    foreach ($worklogs as $epic => $issues) {
        foreach ($issues as $issueKey => $issue) {
            $info = [];
            foreach ($issue['worklogs'] as $worklog) {
                foreach ($worklog['contents'] as $content) {
                    $info[] = sprintf("%s", $content);
                }
            }

            $result[] = [
                'title' => sprintf("[%s] %s",
                    !empty($issue['parent']) ?
                        sprintf('%s > %s', $issue['parent'], $issueKey) :
                        $issueKey,
                    $issue['summary']
                ),
                'info' => $info,
            ];
        }
    }

    return $result;
}
