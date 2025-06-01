<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:send-email',
    description: 'Send a email with statistics',
)]
class SendEmailCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(private MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addOption('recipient', 'r', InputOption::VALUE_OPTIONAL, 'Email recipient', 'anrivarshanidze2407@gmail.com')
            ->addOption('subject', 's', InputOption::VALUE_OPTIONAL, 'Email subject', 'Anri Varshanidze - Weekly statistics');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $recipient = $input->getOption('recipient');
        $subject = $input->getOption('subject');
        
        // Get total number of news articles
        $newsCount = $this->getNewsCount();
        $io->info("Found {$newsCount} news items");
        
        // Prepare and send email
        $email = (new Email())
            ->from('varshanadev@gmail.com')
            ->to($recipient)
            ->subject($subject)
            ->html($this->getBeautifulEmailTemplate($newsCount));

        try {
            // Send the email
            $this->mailer->send($email);
            $io->success('Email with statistics sent successfully!');
        } catch (\Exception $e) {
            $io->error('Failed to send email: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
    
    /**
     * Get the total number of news items from database
     */
    private function getNewsCount(): int
    {
        try {
            $query = $this->entityManager->createQuery('SELECT COUNT(n.id) FROM App\Entity\News n');
            return (int) $query->getSingleScalarResult();
        } catch (\Exception $e) {
            // Log error or handle it as needed
            return 0;
        }
    }
    
    private function getBeautifulEmailTemplate(int $newsCount): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Weekly Statistics</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td style="padding: 20px 0; text-align: center; background-color: #3498db;">
                <h1 style="color: white; margin: 0;">Weekly Statistics</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <table role="presentation" width="100%" style="max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #3498db; margin-top: 0;">Hello from Symfony!</h2>
                            <p style="line-height: 1.6; color: #333;">Here is your weekly news statistics update.</p>
                            
                            <div style="margin: 25px 0; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #3498db; border-radius: 4px;">
                                <h3 style="color: #3498db; margin-top: 0;">News Statistics</h3>
                                <p style="font-size: 18px; font-weight: bold;">Total News Items: {$newsCount}</p>
                                <div style="background-color: #e9f7fe; padding: 10px; border-radius: 4px; margin-top: 10px;">
                                    <p style="margin: 0; color: #2980b9;">Stay updated with the latest {$newsCount} news items in our database!</p>
                                </div>
                            </div>
                            
                            <p style="line-height: 1.6; color: #333;">We hope you find this information helpful. Stay tuned for more updates next week!</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center; color: #777; font-size: 12px;">
                <p>&copy; 2023 Symfony Newsletter. All rights reserved.</p>
                <p>If you no longer wish to receive these emails, you can <a href="#" style="color: #3498db;">unsubscribe</a>.</p>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
