<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CandidatesAndJobsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:candidates_and_jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importing candidates list and their jobs list';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->candidatesImport();
        $this->jobsImport();
        $this->getCandidateJobsList();
        
        return $this->info("Candidates and Jobs are Imported!");
    }

    public function candidatesImport(): void
    {
        try {
            // Reading candidates file
            $filename = storage_path('app/public/candidates.csv');
            $file = fopen($filename, "r");
            $candidates = array();
            while ( ($cdata = fgetcsv($file)) !==FALSE ) {
                $candidate_data['first_name'] = $cdata[1];
                $candidate_data['last_name'] = $cdata[2];
                $candidate_data['email'] = $cdata[3];

                array_push($candidates, $candidate_data);
            } 

            // Preparing candidates data to insert
            $candidate_insert_data = [];
            foreach($candidates as $candidate){
                $is_candidate_exist = Candidate::where('email', $candidate['email'])->first();

                if(!$is_candidate_exist){
                    $candidate_insert_data[] = [
                        'email' => $candidate['email'],
                        'first_name' => $candidate['first_name'],
                        'last_name' => $candidate['last_name'],
                    ];
                }
            }

            // Inserting Candidates
            if(sizeof($candidate_insert_data) > 0){
                Candidate::insert($candidate_insert_data);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function jobsImport(): void
    {
        try {
            // Reading jobs file
            $filename_2 = storage_path('app/public/jobs.csv');
            $file_2 = fopen($filename_2, "r");
            $jobs = array();
            while ( ($jdata = fgetcsv($file_2)) !==FALSE ) {
                $job_data['candidate_id'] = $jdata[1];
                $job_data['job_title'] = $jdata[2];
                $job_data['company_name'] = $jdata[3];
                $job_data['start_date'] = Carbon::parse($jdata[4])->format('Y-m-d H:i:s');
                $job_data['end_date'] = Carbon::parse($jdata[5])->format('Y-m-d H:i:s');

                array_push($jobs, $job_data);
            } 

            // Preparing jobs data to insert
            $job_insert_data = [];
            foreach($jobs as $job){
                $is_job_exist = Job::where('candidate_id', $job['candidate_id'])
                                        ->where('job_title', $job['job_title'])
                                        ->where('company_name', $job['company_name'])
                                        ->where('start_date', $job['start_date'])
                                        ->where('end_date', $job['end_date'])
                                        ->first();

                if(!$is_job_exist){
                    $job_insert_data[] = [
                        'candidate_id' => $job['candidate_id'],
                        'job_title' => $job['job_title'],
                        'company_name' => $job['company_name'],
                        'start_date' => $job['start_date'],
                        'end_date' => $job['end_date'],
                    ];
                }
            }

            // Inserting Jobs
            if(sizeof($job_insert_data) > 0){
                Job::insert($job_insert_data);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function getCandidateJobsList()
    {
        $candidates = Candidate::all(); 
        foreach($candidates as $candidate) {

            $this->warn("CandidateName: $candidate->first_name $candidate->last_name, Email: $candidate->email");

            $candidate_jobs = Job::where('candidate_id', $candidate->id)
                                ->orderBy('start_date', 'DESC')
                                ->orderBy('end_date', 'DESC')
                                ->limit(3)
                                ->get();

            foreach($candidate_jobs as $candidate_job) {
                $this->line("Job Title: $candidate_job->job_title - Company: $candidate_job->company_name - StartDate: $candidate_job->start_date - EndDate: $candidate_job->end_date");
            }
        }
    }
}
