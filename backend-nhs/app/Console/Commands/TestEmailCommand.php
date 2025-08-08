<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email} {--code=123456} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tester l\'envoi d\'email de vérification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $code = $this->option('code');
        $name = $this->option('name');

        $this->info("🧪 Test d'envoi d'email...");
        $this->info("📧 Destinataire: {$email}");
        $this->info("🔢 Code: {$code}");
        
        if ($name) {
            $this->info("👤 Nom: {$name}");
        }

        try {
            Mail::to($email)->send(new VerificationCodeMail($code, $name));
            
            $this->info("✅ Email envoyé avec succès !");
            $this->info("📬 Vérifiez votre boîte de réception");
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'envoi:");
            $this->error($e->getMessage());
            
            $this->newLine();
            $this->warn("💡 Vérifiez votre configuration SMTP dans .env:");
            $this->warn("   - MAIL_HOST");
            $this->warn("   - MAIL_PORT");
            $this->warn("   - MAIL_USERNAME");
            $this->warn("   - MAIL_PASSWORD");
            $this->warn("   - MAIL_ENCRYPTION");
            
            return 1;
        }

        return 0;
    }
}
