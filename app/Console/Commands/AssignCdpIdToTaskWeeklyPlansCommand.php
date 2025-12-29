<?php

namespace App\Console\Commands;

use App\Models\PlantationControl;
use App\Models\TaskWeeklyPlan;
use Illuminate\Console\Command;

class AssignCdpIdToTaskWeeklyPlansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-cdp-id-to-task-weekly-plans-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = TaskWeeklyPlan::where('plantation_control_id', null)->get();
        $cdps = PlantationControl::all();

        foreach ($tasks as $task) {
            $lote_id = $task->lotePlantationControl->id;
            $cdp = $cdps->where('lote_id', $lote_id)->where('status', 1)->first();

            if (!$cdp) {
                $task->plantation_control_id = 1;
            }else{
                $task->plantation_control_id = $cdp->id;
            }

            $task->save();
        }
    }
}
