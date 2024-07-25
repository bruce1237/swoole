<?php
namespace server\monitor;

class MonitorServer
{
    const PORTS =[
        9501,
        9502,
        9503
    ];

    public function __construct()
    {
        // $reports = $this->monitorPorts(self::PORTS);
        // $result = $this->analysisPortsMonitorReport($reports);
        // var_dump($result);
        
    }

    public function monitorPorts(array $ports): array
    {
        $reports = array();
        foreach($ports as $port){
            $command = "netstat -anp 2>/dev/null | grep :{$port} | grep LISTEN | wc -l";
            $reports[$port] = (int)shell_exec($command);
        }
        return $reports;
    }

    public function analysisPortsMonitorReport(array $reports): array
    {
        $portsFailed = [];
        foreach($reports as $port => $active) {
            if ($active!=1) {
                $portsFailed[] = $port;
            }
        }
        return $portsFailed;
    }

    public function startMonitor()
    {
        $reports = $this->monitorPorts(self::PORTS);
        $result = $this->analysisPortsMonitorReport($reports);
        var_dump($result);
        file_put_contents("./a.txt", json_encode($reports),FILE_APPEND);
    }
}


// $monitorServer = new MonitorServer();
// swoole_timer_tick(2000, function() use ($monitorServer){
//     $monitorServer->startMonitor();
// });
