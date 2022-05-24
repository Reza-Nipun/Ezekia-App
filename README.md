<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://civicminds.com/wp-content/uploads/2019/01/Candidate-Screening-Process-1.png" width="400"></a></p>

## Instruction

In order to install and run the system please follow the following steps:

- Clone this project: <code>git clone https://github.com/Reza-Nipun/Ezekia-App.git</code>
- Create and edit your own .env file: <code>cp .env.example .env</code>
- Run <code>./vendor/bin/sail build --no-cache</code> inside the project directory terminal
- Run <code>./vendor/bin/sail up -d</code>
- Table migration: <code>./vendor/bin/sail artisan migrate</code>
- Put the Candidates.csv and Jobs.csv files inside storage/app/public directory
- Run command in CLI mode: <code>./vendor/bin/sail artisan import:candidates_and_jobs</code>

