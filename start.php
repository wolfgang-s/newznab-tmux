<?php
require_once(dirname(__FILE__)."/bin/config.php");
require_once(dirname(__FILE__)."/lib/ColorCLI.php");
require_once (WWW_DIR.'/lib/site.php');
require_once(WWW_DIR.'/lib/Tmux.php');

$db = new DB();
$DIR = dirname (__FILE__);
$c = new ColorCLI();
$s = new Sites();
$site = $s->get();
$t = new Tmux();
$tmux = $t->get();
$patch = (isset($tmux->sqlpatch)) ? $tmux->sqlpatch : 0;

// Check database patch version
if ($patch < 25) {
	exit($c->error("\nYour database is not up to date. Please update.\nphp ${DIR}/lib/DB/patchDB.php\n"));
}
$tmux_session = (isset($tmux->tmux_session)) ? $tmux->tmux_session : 0;
$seq = (isset($tmux->sequential)) ? $tmux->sequential : 0;
$powerline = (isset($tmux->powerline)) ? $tmux->powerline : 0;
$colors = (isset($tmux->colors)) ? $tmux->colors : 0;
$import = (isset($tmux->import)) ? $tmux->import : 0;

//check if session exists
$exec = exec("tmux list-session | grep $tmux_session", $session);
if (count($session) !== 0) {
	exit($c->error("tmux session:" . $tmux_session . " is already running, aborting.Attach to session by typing tmux att\n"));
} else {
	echo $c->notice("The above is a notice generated by tmux. \nWhen starting this script, we first check that you do not have a tmux session currently running. If you do not, the above notice is generated by tmux.\nIt is not an error.");
}

function writelog($pane)
{
	$path = dirname(__FILE__) . "/bin/logs";
	$getdate = gmDate("Ymd");
	$tmux = new Tmux();
	$logs = $tmux->get()->write_logs;
	if ($logs == 1) {
		return "2>&1 | tee -a $path/$pane-$getdate.log";
	} else {
		return "";
	}
}

//remove folders from tmpunrar
$tmpunrar = $site->tmpunrarpath;
if ((count(glob("$tmpunrar/*", GLOB_ONLYDIR))) > 0) {
	echo $c->info("Removing dead folders from " . $tmpunrar);
	exec("rm -r " . $tmpunrar . "/*");
}

function command_exist($cmd)
{
	$returnVal = exec("which $cmd 2>/dev/null");
	return (empty($returnVal) ? false : true);
}

//check for apps
$apps = array("time", "tmux", "nice", "python", "tee");
foreach ($apps as &$value) {
	if (!command_exist($value)) {
		exit($c->error("Tmux scripts require " . $value . " but it's not installed. Aborting.\n"));
	}
}

function python_module_exist($module)
{
	$output = $returnCode = '';
	exec("python -c \"import $module\"", $output, $returnCode);
	return ($returnCode == 0 ? true : false);
}

function start_apps($tmux_session)
{
	$t = new Tmux();
	$tmux = $t->get();
	$htop = $tmux->htop;
	$vnstat = $tmux->vnstat;
	$vnstat_args = $tmux->vnstat_args;
	$tcptrack = $tmux->tcptrack;
	$tcptrack_args = $tmux->tcptrack_args;
	$nmon = $tmux->nmon;
	$bwmng = $tmux->bwmng;
	$mytop = $tmux->mytop;
	$showprocesslist = $tmux->showprocesslist;
	$processupdate = $tmux->processupdate;
	$console_bash = $tmux->console;

	if ($htop == 1 && command_exist("htop")) {
		exec("tmux new-window -t $tmux_session -n htop 'printf \"\033]2;htop\033\" && htop'");
	}

	if ($nmon == 1 && command_exist("nmon")) {
		exec("tmux new-window -t $tmux_session -n nmon 'printf \"\033]2;nmon\033\" && nmon -t'");
	}

	if ($vnstat == 1 && command_exist("vnstat")) {
		exec("tmux new-window -t $tmux_session -n vnstat 'printf \"\033]2;vnstat\033\" && watch -n10 \"vnstat ${vnstat_args}\"'");
	}

	if ($tcptrack == 1 && command_exist("tcptrack")) {
		exec("tmux new-window -t $tmux_session -n tcptrack 'printf \"\033]2;tcptrack\033\" && tcptrack ${tcptrack_args}'");
	}

	if ($bwmng == 1 && command_exist("bwm-ng")) {
		exec("tmux new-window -t $tmux_session -n bwm-ng 'printf \"\033]2;bwm-ng\033\" && bwm-ng'");
	}

	if ($mytop == 1 && command_exist("mytop")) {
		exec("tmux new-window -t $tmux_session -n mytop 'printf \"\033]2;mytop\033\" && mytop -u'");
	}

	if ($showprocesslist == 1) {
		exec("tmux new-window -t $tmux_session -n showprocesslist 'printf \"\033]2;showprocesslist\033\" && watch -n .5 \"mysql -e \\\"SELECT time, state, info FROM information_schema.processlist WHERE command != \\\\\\\"Sleep\\\\\\\" AND time >= $processupdate ORDER BY time DESC \\\G\\\"\"'");
	}
	//exec("tmux new-window -t $tmux_session -n showprocesslist 'printf \"\033]2;showprocesslist\033\" && watch -n .2 \"mysql -e \\\"SELECT time, state, rows_examined, info FROM information_schema.processlist WHERE command != \\\\\\\"Sleep\\\\\\\" AND time >= $processupdate ORDER BY time DESC \\\G\\\"\"'");

	if ($console_bash == 1) {
		exec("tmux new-window -t $tmux_session -n bash 'printf \"\033]2;Bash\033\" && bash -i'");
	}
}

function window_utilities($tmux_session)
{
	exec("tmux new-window -t $tmux_session -n Utils 'printf \"\033]2;update_predb\033\"'");
	exec("tmux selectp -t 0;tmux splitw -t $tmux_session:1 -v -p 75 'printf \"\033]2;sphinx\033\"'");
    exec("tmux splitw -t $tmux_session:1 -v -p 67 'printf \"\033]2;update_missing_movie_info\033\"'");
	exec("tmux selectp -t 0; tmux splitw -t $tmux_session:1 -h -p 50 'printf \"\033]2;update_tv\033\"'");
	exec("tmux selectp -t 2; tmux splitw -t $tmux_session:1 -h -p 50 'printf \"\033]2;comment_sharing\033\"'");
    exec("tmux selectp -t 4; tmux splitw -t $tmux_session:1 -h -p 50 'printf \"\033]2;nzbcount\033\"'");

}

function window_colors($tmux_session)
{
	exec("tmux new-window -t $tmux_session -n colors 'printf \"\033]2;tmux_colors\033\"'");
}

function window_post($tmux_session)
{
	exec("tmux new-window -t $tmux_session -n PostProcessing 'printf \"\033]2;processNfosOld\033\"'");
	exec("tmux splitw -t $tmux_session:2 -h -p 50 'printf \"\033]2;processGames\033\"'");
	exec("tmux selectp -t 0; tmux splitw -t $tmux_session:2 -v -p 80 'printf \"\033]2;processTV\033\"'");
    exec("tmux splitw -t $tmux_session:2 -v -p 75 'printf \"\033]2;processMovies\033\"'");
    exec("tmux splitw -t $tmux_session:2 -v -p 67 'printf \"\033]2;processMusic\033\"'");
    exec("tmux splitw -t $tmux_session:2 -v -p 50 'printf \"\033]2;processAnime\033\"'");
    exec("tmux selectp -t 5;tmux splitw -t $tmux_session:2 -v -p 80 'printf \"\033]2;processSpotnab\033\"'");
    exec("tmux splitw -t $tmux_session:2 -v -p 75 'printf \"\033]2;processBooks\033\"'");
    exec("tmux splitw -t $tmux_session:2 -v -p 67 'printf \"\033]2;processOther\033\"'");
    exec("tmux splitw -t $tmux_session:2 -v -p 50 'printf \"\033]2;processUnwanted\033\"'");
}

function window_fixnames($tmux_session)
{
	exec("tmux new-window -t $tmux_session -n FixNames 'printf \"\033]2;Fix_Release_Names\033\"'");
	exec("tmux selectp -t 0; tmux splitw -t $tmux_session:3 -v -p 50 'printf \"\033]2;RemoveCrap\033\"'");
    exec("tmux selectp -t 0; tmux splitw -t $tmux_session:3 -h -p 50 'printf \"\033]2;PreDB_Hash_Decrypt\033\"'");
    exec("tmux selectp -t 1; tmux splitw -t $tmux_session:3 -v -p 50 'printf \"\033]2;RequestID\033\"'");
    exec("tmux selectp -t 3;tmux splitw -t $tmux_session:3 -h -p 50 'printf \"\033]2;PrehashUpdate\033\"'");
}

function window_ircscraper($tmux_session, $window)
{
    $t = new Tmux();
	$tmux = $t->get();
	$scrape_cz = $tmux->scrape_cz;
	$scrape_efnet = $tmux->scrape_efnet;

	if ($scrape_cz == 1 && $scrape_efnet == 1) {
	    exec("tmux new-window -t $tmux_session -n IRCScraper 'printf \"\033]2;scrape_cz\033\"'");
		exec("tmux selectp -t 0; tmux splitw -t $tmux_session:$window -v -p 50 'printf \"\033]2;scrape_Efnet\033\"'");
	}
	else if ($scrape_cz == 1) {
		exec("tmux new-window -t $tmux_session -n IRCScraper 'printf \"\033]2;scrape_cz\033\"'");
	}
	elseif ($scrape_efnet == 1) {
		exec("tmux new-window -t $tmux_session -n IRCScraper 'printf \"\033]2;scrape_Efnet\033\"'");
	}
}


function attach($DIR, $tmux_session)
{
	if (command_exist("php5")) {
		$PHP = "php5";
	} else {
		$PHP = "php";
	}

	//get list of panes by name
	$panes_win_1 = exec("echo `tmux list-panes -t $tmux_session:0 -F '#{pane_title}'`");
	$panes0 = str_replace("\n", '', explode(" ", $panes_win_1));
	$log = writelog($panes0[0]);
	exec("tmux respawnp -t $tmux_session:0.0 '$PHP " . $DIR . "/bin/monitor.php $log'");
	exec("tmux select-window -t $tmux_session:0; tmux attach-session -d -t $tmux_session");
}

//create tmux session
if ($powerline == 1) {
	$tmuxconfig = $DIR . "/powerline/tmux.conf";
} else {
	$tmuxconfig = $DIR . "/conf/tmux.conf";
}

if ($seq == 1) {
	exec("tmux -f $tmuxconfig new-session -d -s $tmux_session -n Monitor 'printf \"\033]2;\"Monitor\"\033\"'");
	exec("tmux selectp -t 0; tmux splitw -t $tmux_session:0 -h -p 67 'printf \"\033]2;update_binaries\033\"'");
    exec("tmux selectp -t 0; tmux splitw -t $tmux_session:0 -v -p 30 'printf \"\033]2;postprocessing\033\"'");
    exec("tmux selectp -t 2; tmux splitw -t $tmux_session:0 -v -p 75 'printf \"\033]2;backfill\033\"'");
    exec("tmux splitw -t $tmux_session:0 -v -p 67 'printf \"\033]2;import-nzb\033\"'");
    exec("tmux splitw -t $tmux_session:0 -v -p 50 'printf \"\033]2;update_releases\033\"'");

	window_utilities($tmux_session);
	window_post($tmux_session);
    window_fixnames($tmux_session);
    window_ircscraper($tmux_session, 4);

	if ($colors == 1) {
		window_colors($tmux_session);
	}
	start_apps($tmux_session);
	attach($DIR, $tmux_session);
} else {
	exec("tmux -f $tmuxconfig new-session -d -s $tmux_session -n Monitor 'printf \"\033]2;\"Monitor\"\033\"'");
	exec("tmux selectp -t 0; tmux splitw -t $tmux_session:0 -h -p 67 'printf \"\033]2;update_binaries\033\"'");
    exec("tmux selectp -t 0; tmux splitw -t $tmux_session:0 -v -p 30 'printf \"\033]2;postprocessing\033\"'");
    exec("tmux selectp -t 2; tmux splitw -t $tmux_session:0 -v -p 75 'printf \"\033]2;backfill\033\"'");
    exec("tmux splitw -t $tmux_session:0 -v -p 67 'printf \"\033]2;import-nzb\033\"'");
    exec("tmux splitw -t $tmux_session:0 -v -p 50 'printf \"\033]2;update_releases\033\"'");

	window_utilities($tmux_session);
	window_post($tmux_session);
    window_fixnames($tmux_session);
    window_ircscraper($tmux_session, 4);

	if ($colors == 1) {
		window_colors($tmux_session);
	}
	start_apps($tmux_session);
	attach($DIR, $tmux_session);
}