<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use App\Models\Review;
use App\Models\Event;
use App\Models\Document;
use App\Models\RequisitionsVersion;
use App\Models\ReviewsVersion;
use App\Enums\RoleId;
use App\Enums\DocumentType;
use App\Enums\EventType;

class MigrateOldSystemData extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'migrate:old-system {--dry-run : Preview what would be migrated without making changes}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Migrate data from the old system database to the current system';

	/**
	 * Old database connection configuration
	 * Configure using environment variables in your .env file
	 */
	private function getOldDbConfig()
	{
		return [
			'host' => env('OLD_DB_HOST'),
			'port' => env('OLD_DB_PORT', '3306'),
			'database' => env('OLD_DB_NAME'),
			'username' => env('OLD_DB_USERNAME'),
			'password' => env('OLD_DB_PASSWORD'),
		];
	}

	public function handle()
	{
		$isDryRun = $this->option('dry-run');

		if ($isDryRun) {
			$this->info('🔍 DRY RUN MODE - No data will be modified');
		}

		$this->info('🚀 Starting migration from old system...');

		try {
			// Prepare database first
			if (!$isDryRun) {
				$this->prepareDatabaseForMigration();
			}

			// Configure old database connection
			$this->configureOldDatabaseConnection();

			// Test old database connection
			if (!$this->testOldDatabaseConnection()) {
				throw new \Exception('Could not connect to old database. Please check your credentials.');
			}

			$this->info('✅ Successfully connected to old database');

			// Show migration plan
			$this->showMigrationPlan();

			if (!$isDryRun && !$this->confirm('Do you want to proceed with the migration?')) {
				$this->info('Migration cancelled.');
				return 0;
			}

			// Start migration process
			DB::transaction(function () use ($isDryRun) {
				$this->migrateUsers($isDryRun);
				$this->migrateRequisitions($isDryRun);
				$this->migrateTakenDisciplines($isDryRun);
				$this->migrateTakenDisciplinesVersions($isDryRun);
				$this->migrateReviews($isDryRun);
				$this->migrateEvents($isDryRun);
				$this->migrateDocuments($isDryRun);
				$this->migrateVersions($isDryRun);
			});

			$this->info('✅ Migration completed successfully!');
		} catch (\Exception $e) {
			$this->error('❌ Migration failed: ' . $e->getMessage());
			Log::error('Migration failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
			return 1;
		}

		return 0;
	}

	private function prepareDatabaseForMigration()
	{
		$this->info('🗄️ Preparing database for migration...');
		
		if (!$this->confirm('This will run migrate:fresh and db:seed, which will DELETE ALL existing data. Are you sure?')) {
			throw new \Exception('Migration cancelled by user.');
		}

		$this->info('Running migrate:fresh...');
		$exitCode = Artisan::call('migrate:fresh', ['--force' => true]);
		
		if ($exitCode !== 0) {
			throw new \Exception('migrate:fresh failed with exit code: ' . $exitCode);
		}

		$this->info('Running db:seed...');
		$exitCode = Artisan::call('db:seed', ['--force' => true]);
		
		if ($exitCode !== 0) {
			throw new \Exception('db:seed failed with exit code: ' . $exitCode);
		}

		$this->info('✅ Database prepared successfully');
	}

	private function configureOldDatabaseConnection()
	{
		$oldDbConfig = $this->getOldDbConfig();

		// Validate required environment variables
		if (!$oldDbConfig['host'] || !$oldDbConfig['database'] || !$oldDbConfig['username']) {
			throw new \Exception('Missing required old database configuration. Please set OLD_DB_HOST, OLD_DB_NAME, and OLD_DB_USERNAME in your .env file.');
		}

		config(['database.connections.old_system' => [
			'driver' => 'mysql',
			'host' => $oldDbConfig['host'],
			'port' => $oldDbConfig['port'],
			'database' => $oldDbConfig['database'],
			'username' => $oldDbConfig['username'],
			'password' => $oldDbConfig['password'],
			'charset' => 'utf8mb4',
			'collation' => 'utf8mb4_unicode_ci',
			'prefix' => '',
			'strict' => true,
			'engine' => null,
		]]);
	}

	private function testOldDatabaseConnection(): bool
	{
		try {
			DB::connection('old_system')->getPdo();
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	private function showMigrationPlan()
	{
		$this->info('📋 Migration Plan:');
		$this->line('1. Users (from old users table)');
		$this->line('2. Requisitions (from old requisitions table)');
		$this->line('3. Taken Disciplines (from old taken_disciplines table)');
		$this->line('4. Taken Disciplines Versions (from old taken_disciplines_version table)');
		$this->line('5. Reviews (from old reviews table)');
		$this->line('6. Events (from old events table)');
		$this->line('7. Documents (from old documents table)');
		$this->line('8. Requisitions Versions (from old requisitions_versions table)');
		$this->line('9. Reviews Versions (from old reviews_versions table)');
		$this->line('');
	}

	private function migrateUsers($isDryRun = false)
	{
		$this->info('👥 Migrating Users...');

		$oldUsers = DB::connection('old_system')->table('users')->get();

		$this->line("Found {$oldUsers->count()} users in old system");

		if ($isDryRun) {
			$this->line('Would migrate users...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldUsers as $oldUser) {
			// Check if user already exists by ID or codpes
			$existingUser = User::where('id', $oldUser->id)->orWhere('codpes', $oldUser->codpes)->first();

			if ($existingUser) {
				$this->warn("User {$oldUser->id} already exists in new system, skipping...");
				$skippedCount++;
				continue;
			}

			try {
				$newUser = new User();
				$newUser->id = $oldUser->id; // Preserve original ID
				$newUser->name = $oldUser->name;
				$newUser->email = $oldUser->email;
				$newUser->email_verified_at = $oldUser->email_verified_at;
				$newUser->password = $oldUser->password;
				$newUser->remember_token = $oldUser->remember_token;
				$newUser->codpes = $oldUser->codpes;

				// Map old current_role_id to new role system
				$newUser->current_role_id = 1;
				$newUser->current_department_id = null;

				$newUser->created_at = $oldUser->created_at;
				$newUser->updated_at = $oldUser->updated_at;

				$newUser->save();

				$newUser->assignRole(RoleId::STUDENT);
				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate user {$oldUser->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} users, skipped {$skippedCount} existing users");
	}
	private function migrateRequisitions($isDryRun = false)
	{
		$this->info('📋 Migrating Requisitions...');

		$oldRequisitions = DB::connection('old_system')->table('requisitions')->get();

		$this->line("Found {$oldRequisitions->count()} requisitions in old system");

		if ($isDryRun) {
			$this->line('Would migrate requisitions...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldRequisitions as $oldReq) {
			$existingReq = Requisition::where('id', $oldReq->id)->first();

			if ($existingReq) {
				$this->warn("Requisition {$oldReq->id} already exists in new system, skipping...");
				$skippedCount++;
				continue;
			}

			try {
				$newReq = new Requisition();
				$newReq->id = $oldReq->id;
				$newReq->department = $oldReq->department;
				$newReq->student_nusp = $oldReq->student_nusp;
				$newReq->latest_version = $oldReq->latest_version;
				$newReq->student_name = $oldReq->student_name;
				$newReq->email = $oldReq->email;
				$newReq->course = $oldReq->course;
				$newReq->requested_disc = $oldReq->requested_disc;
				$newReq->requested_disc_type = $oldReq->requested_disc_type;
				$newReq->situation = $oldReq->situation;
				$newReq->internal_status = $oldReq->internal_status;
				$newReq->requested_disc_code = $oldReq->requested_disc_code;
				$newReq->observations = $oldReq->observations;
				$newReq->result = $oldReq->result;
				$newReq->result_text = $oldReq->result_text;

				$newReq->editable = $oldReq->situation == "Retornado para o aluno devido a inconsistência nos dados";
				$newReq->registered = $oldReq->registered === "Sim";

				$newReq->created_at = $oldReq->created_at;
				$newReq->updated_at = $oldReq->updated_at;

				$newReq->save(['id']); // Save with explicit id

				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate requisition {$oldReq->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} requisitions, skipped {$skippedCount} existing requisitions");
	}

	private function migrateTakenDisciplines($isDryRun = false)
	{
		$this->info('📚 Migrating Taken Disciplines...');

		$oldTakenDiscs = DB::connection('old_system')->table('taken_disciplines')->get();

		$this->line("Found {$oldTakenDiscs->count()} taken disciplines in old system");

		if ($isDryRun) {
			$this->line('Would migrate taken disciplines...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldTakenDiscs as $oldDisc) {
			$requisition = DB::table('requisitions')
				->where('id', $oldDisc->requisition_id)
				->first();

			if (!$requisition) {
				$this->warn("Could not find requisition for taken discipline {$oldDisc->id}");
				$skippedCount++;
				continue;
			}

			$existingDisc = TakenDisciplines::where('id', $oldDisc->id)
				->first();

			if ($existingDisc) {
				$this->warn("Taken discipline {$oldDisc->id} already exists in new system, skipping...");
				$skippedCount++;
				continue;
			}

			try {
				$newDisc = new TakenDisciplines();
				$newDisc->id = $oldDisc->id;
				$newDisc->requisition_id = $requisition->id;
				$newDisc->name = $oldDisc->name;
				$newDisc->code = $oldDisc->code;
				$newDisc->year = $oldDisc->year;
				$newDisc->semester = $oldDisc->semester;
				$newDisc->grade = $oldDisc->grade;
				$newDisc->institution = $oldDisc->institution;
				$newDisc->version = $oldDisc->latest_version;
				$newDisc->created_at = $oldDisc->created_at;
				$newDisc->updated_at = $oldDisc->updated_at;

				$newDisc->save();
				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate taken discipline {$oldDisc->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} taken disciplines, skipped {$skippedCount} existing disciplines");
	}

	private function migrateTakenDisciplinesVersions($isDryRun = false)
	{
		$this->info('📚 Migrating Taken Disciplines Versions...');

		$oldTakenDiscVersions = DB::connection('old_system')->table('taken_disciplines_versions')->get();

		$this->line("Found {$oldTakenDiscVersions->count()} taken disciplines in old system");

		if ($isDryRun) {
			$this->line('Would migrate taken disciplines versions...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldTakenDiscVersions as $oldDisc) {
			$requisition = DB::table('requisitions')
				->where('id', $oldDisc->requisition_id)
				->first();

			if (!$requisition) {
				$this->warn("Could not find requisition for taken discipline {$oldDisc->id}");
				$skippedCount++;
				continue;
			}

			$existingDisc = TakenDisciplines::where('requisition_id', $oldDisc->requisition_id)
				->where('version', $oldDisc->version)
				->where('name', $oldDisc->name)
				->where('code', $oldDisc->code)
				->first();

			if ($existingDisc) {
				$this->warn("Taken discipline version with requisition Id {$oldDisc->requisition_id} with version {$oldDisc->version} already exists in new system, skipping...");
			}

			try {
				$newDisc = new TakenDisciplines();
				$newDisc->requisition_id = $requisition->id;
				$newDisc->name = $oldDisc->name;
				$newDisc->code = $oldDisc->code;
				$newDisc->year = $oldDisc->year;
				$newDisc->semester = $oldDisc->semester;
				$newDisc->grade = $oldDisc->grade;
				$newDisc->institution = $oldDisc->institution;
				$newDisc->version = $oldDisc->version;
				$newDisc->created_at = $oldDisc->created_at;
				$newDisc->updated_at = $oldDisc->updated_at;

				$newDisc->save();
				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate taken discipline {$oldDisc->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} taken disciplines versions, skipped {$skippedCount} existing disciplines versions");
	}

	private function migrateReviews($isDryRun = false)
	{
		$this->info('⭐ Migrating Reviews...');

		$oldReviews = DB::connection('old_system')->table('reviews')->get();

		$this->line("Found {$oldReviews->count()} reviews in old system");

		if ($isDryRun) {
			$this->line('Would migrate reviews...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldReviews as $oldReview) {
			$requisition = DB::table('requisitions')
				->where('id', $oldReview->requisition_id)
				->first();

			if (!$requisition) {
				$this->warn("Could not find requisition for review {$oldReview->id}");
				$skippedCount++;
				continue;
			}

			$existingReview = Review::where('id', $oldReview->id)
				->first();

			if ($existingReview) {
				$this->warn("Review {$oldReview->id} already exists in new system, skipping...");
				$skippedCount++;
				continue;
			}

			try {
				$newReview = new Review();
				$newReview->id = $oldReview->id;
				$newReview->requisition_id = $requisition->id;
				$newReview->reviewer_decision = $oldReview->reviewer_decision;
				$newReview->justification = $oldReview->justification;
				$newReview->reviewer_nusp = $oldReview->reviewer_nusp;
				$newReview->reviewer_name = $oldReview->reviewer_name;
				$newReview->latest_version = $oldReview->latest_version;
				$newReview->created_at = $oldReview->created_at;
				$newReview->updated_at = $oldReview->updated_at;

				$newReview->save();
				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate review {$oldReview->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} reviews, skipped {$skippedCount} existing reviews");
	}

	private function migrateEvents($isDryRun = false)
	{
		$this->info('📅 Migrating Events...');

		$oldEvents = DB::connection('old_system')->table('events')->get();

		$this->line("Found {$oldEvents->count()} events in old system");

		if ($isDryRun) {
			$this->line('Would migrate events...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldEvents as $oldEvent) {
			$requisition = DB::table('requisitions')
				->where('id', $oldEvent->requisition_id)
				->first();

			if (!$requisition) {
				$this->warn("Could not find requisition for event {$oldEvent->id}");
				$skippedCount++;
				continue;
			}

			$existingEvent = Event::where('id', $oldEvent->id)
				->first();

			if ($existingEvent) {
				$this->warn("Event {$oldEvent->id} already exists in new system, skipping...");
				$skippedCount++;
				continue;
			}

			try {
				$newEvent = new Event();
				$newEvent->id = $oldEvent->id;
				$newEvent->requisition_id = $requisition->id;
				$newEvent->type = $this->mapOldEventType($oldEvent->type);
				$newEvent->message = $oldEvent->message;
				$newEvent->author_name = $oldEvent->author_name;
				$newEvent->author_nusp = $oldEvent->author_nusp;
				$newEvent->version = $oldEvent->version;
				$newEvent->created_at = $oldEvent->created_at;
				$newEvent->updated_at = $oldEvent->updated_at;

				$newEvent->save();
				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate event {$oldEvent->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} events, skipped {$skippedCount} existing events");
	}

	private function migrateDocuments($isDryRun = false)
	{
		$this->info('📄 Migrating Documents...');

		$oldDocuments = DB::connection('old_system')->table('documents')
			->orderBy('requisition_id')
			->orderBy('type')
			->orderBy('created_at')
			->get();

		$this->line("Found {$oldDocuments->count()} documents in old system");

		if ($isDryRun) {
			$this->line('Would migrate documents...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		// Group documents by requisition_id and type to calculate versions
		$documentGroups = $oldDocuments->groupBy(function ($doc) {
			return $doc->requisition_id . '_' . $doc->type;
		});

		foreach ($documentGroups as $groupKey => $documentsInGroup) {
			$version = 1;

			foreach ($documentsInGroup as $oldDoc) {
				$requisition = DB::table('requisitions')
					->where('id', $oldDoc->requisition_id)
					->first();

				if (!$requisition) {
					$this->warn("Could not find requisition for document {$oldDoc->id}");
					$skippedCount++;
					continue;
				}

				$existingDoc = Document::where('id', $oldDoc->id)
					->first();

				if ($existingDoc) {
					$this->warn("Document {$oldDoc->id} already exists in new system, skipping...");
					$skippedCount++;
					$version++;
					continue;
				}

				try {
					$newDoc = new Document();
					$newDoc->id = $oldDoc->id;
					$newDoc->requisition_id = $requisition->id;
					$newDoc->type = $oldDoc->type;

					// Migrate path from old "test/<name>" to new "documents/<name>"
					$newDoc->path = preg_replace('#^test/#', 'documents/', $oldDoc->path);

					$filePath = Storage::disk('local')->path($newDoc->path);

					if (!file_exists($filePath)) {
						$skippedCount++;
						$this->warn("Document {$oldDoc->id} file not found at path: {$newDoc->path}");
						continue;
					}

					$newDoc->version = $version;

					$newDoc->hash = hash_file('sha256', $filePath);
					$newDoc->created_at = $oldDoc->created_at;
					$newDoc->updated_at = $oldDoc->updated_at;

					$newDoc->save();
					$migratedCount++;

					$version++;
				} catch (\Exception $e) {
					$this->error("Failed to migrate document {$oldDoc->id}: " . $e->getMessage());
					return 1;
				}
			}
		}

		$this->info("✅ Migrated {$migratedCount} documents, skipped {$skippedCount} existing documents");
	}

	private function migrateVersions($isDryRun = false)
	{
		$this->info('🔄 Migrating Versions...');

		// Migrate Requisitions Versions
		$this->migrateRequisitionsVersions($isDryRun);

		// Migrate Reviews Versions (if exists in old system)
		$this->migrateReviewsVersions($isDryRun);
	}

	private function migrateRequisitionsVersions($isDryRun = false)
	{
		$this->info('📋 Migrating Requisitions Versions...');

		$oldVersions = DB::connection('old_system')->table('requisitions_versions')->get();

		$this->line("Found {$oldVersions->count()} requisition versions in old system");

		if ($isDryRun) {
			$this->line('Would migrate requisition versions...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldVersions as $oldVersion) {
			$requisition = DB::table('requisitions')
				->where('student_nusp', $oldVersion->student_nusp)
				->first();

			if (!$requisition) {
				$this->warn("Could not find requisition for version {$oldVersion->id}");
				$skippedCount++;
				continue;
			}

			$existingVersion = RequisitionsVersion::where('id', $oldVersion->id)
				->first();

			if ($existingVersion) {
				$this->warn("Requisition version {$oldVersion->id} already exists in new system, skipping...");
				$skippedCount++;
				continue;
			}

			try {
				$newVersion = new RequisitionsVersion();
				$newVersion->id = $oldVersion->id;
				$newVersion->requisition_id = $requisition->id;
				$newVersion->department = $oldVersion->department;
				$newVersion->student_nusp = $oldVersion->student_nusp;
				$newVersion->student_name = $oldVersion->student_name;
				$newVersion->email = $oldVersion->email;
				$newVersion->course = $oldVersion->course;
				$newVersion->requested_disc = $oldVersion->requested_disc;
				$newVersion->requested_disc_type = $oldVersion->requested_disc_type;
				$newVersion->requested_disc_code = $oldVersion->requested_disc_code;
				$newVersion->observations = $oldVersion->observations;
				$newVersion->result = $oldVersion->result;
				$newVersion->result_text = $oldVersion->result_text;
				$newVersion->version = $oldVersion->version;

				$newVersion->taken_disciplines_version = $oldVersion->version;

				// Find document versions that were created at the same time as this requisition version
				$versionCreatedAt = $oldVersion->created_at;

				// Get the document version for each document type based on creation time
				$takenDiscRecordVersion = $this->getDocumentVersionAtTime(
					$requisition->id,
					DocumentType::TAKEN_DISCS_RECORD,
					$versionCreatedAt
				);

				$courseRecordVersion = $this->getDocumentVersionAtTime(
					$requisition->id,
					DocumentType::CURRENT_COURSE_RECORD,
					$versionCreatedAt
				);

				$takenDiscSyllabusVersion = $this->getDocumentVersionAtTime(
					$requisition->id,
					DocumentType::TAKEN_DISCS_SYLLABUS,
					$versionCreatedAt
				);

				$requestedDiscSyllabusVersion = $this->getDocumentVersionAtTime(
					$requisition->id,
					DocumentType::REQUESTED_DISC_SYLLABUS,
					$versionCreatedAt
				);

				$newVersion->taken_disc_record_version = $takenDiscRecordVersion;
				$newVersion->course_record_version = $courseRecordVersion;
				$newVersion->taken_disc_syllabus_version = $takenDiscSyllabusVersion;
				$newVersion->requested_disc_syllabus_version = $requestedDiscSyllabusVersion;

				$newVersion->created_at = $oldVersion->created_at;
				$newVersion->updated_at = $oldVersion->updated_at;

				$newVersion->save();
				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate requisition version {$oldVersion->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} requisition versions, skipped {$skippedCount} existing versions");
	}

	private function migrateReviewsVersions($isDryRun = false)
	{
		$oldVersions = DB::connection('old_system')->table('reviews_versions')->get();

		$this->info('📝 Migrating Reviews Versions...');
		$this->line("Found {$oldVersions->count()} review versions in old system");

		if ($isDryRun) {
			$this->line('Would migrate review versions...');
			return;
		}

		$migratedCount = 0;
		$skippedCount = 0;

		foreach ($oldVersions as $oldVersion) {
			$requisition = DB::table('requisitions')
				->where('id', $oldVersion->requisition_id)
				->first();

			if (!$requisition) {
				$this->warn("Could not find requisition for review version {$oldVersion->id}");
				$skippedCount++;
				continue;
			}

			$review = Review::where('requisition_id', $requisition->id)
				->first();

			if (!$review) {
				$this->warn("Could not find review for review version {$oldVersion->id}");
				$skippedCount++;
				continue;
			}

			$existingVersion = ReviewsVersion::where('id', $oldVersion->id)
				->first();

			if ($existingVersion) {
				$this->warn("Review version {$oldVersion->id} already exists in new system, skipping...");
				$skippedCount++;
				continue;
			}

			try {
				$newVersion = new ReviewsVersion();
				$newVersion->id = $oldVersion->id;
				$newVersion->requisition_id = $requisition->id;
				$newVersion->review_id = $review->id;
				$newVersion->reviewer_decision = $oldVersion->reviewer_decision;
				$newVersion->justification = $oldVersion->justification;
				$newVersion->reviewer_nusp = $oldVersion->reviewer_nusp;
				$newVersion->reviewer_name = $oldVersion->reviewer_name;
				$newVersion->version = $oldVersion->version;
				$newVersion->created_at = $oldVersion->created_at;
				$newVersion->updated_at = $oldVersion->updated_at;

				$newVersion->save();
				$migratedCount++;
			} catch (\Exception $e) {
				$this->error("Failed to migrate review version {$oldVersion->id}: " . $e->getMessage());
				return 1;
			}
		}

		$this->info("✅ Migrated {$migratedCount} review versions, skipped {$skippedCount} existing versions");
	}


	/**
	 * Get the document version that was active at a specific time
	 */
	private function getDocumentVersionAtTime($requisitionId, $documentType, $targetTime)
	{
		$document = Document::where('requisition_id', $requisitionId)
			->where('type', $documentType)
			->where('created_at', '<=', $targetTime)
			->orderBy('created_at', 'desc')
			->first();

		if (!$document) {
			$this->warn("No document found for requisition_id {$requisitionId}, type {$documentType} at time {$targetTime}");
			return 1;
		}

		// if ($document->created_at != $targetTime) {
		// 	$this->warn("Document (ID: {$document->id}) for requisition_id {$requisitionId}, type {$documentType} has created_at {$document->created_at} different from targetTime {$targetTime}");
		// }

		return $document->version;
	}

	private function mapOldEventType($oldEventType): string
	{
		$eventMapping = [
			'Encaminhado para a SG pelo aluno' => EventType::SENT_TO_SG,
			'Enviado para análise dos pareceristas' => EventType::SENT_TO_REVIEWERS,
			'Retornado para o aluno devido a inconsistência nos dados' => EventType::BACK_TO_STUDENT,
			'Requerimento deferido' => EventType::ACCEPTED,
			'Requerimento indeferido' => EventType::REJECTED,
			'Retornado por um parecerista' => EventType::RETURNED_BY_REVIEWER,
			'Requerimento em reavaliação' => EventType::IN_REVALUATION,
			'Reenviado pelo aluno depois de atualização' => EventType::UPDATED_BY_STUDENT,
			'Enviado para análise do departamento' => EventType::SENT_TO_DEPARTMENT,
			'Aguardando avaliação da CG' => EventType::REGISTERED,
		];

		return $eventMapping[$oldEventType] ?? $oldEventType;
	}
}
