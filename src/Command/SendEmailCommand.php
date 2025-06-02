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
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;

    }

    protected function configure()
    {
        $this->setDescription('Send email every minute');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('varshanadev@gmail.com')
            ->to('anrivarshanidze2407@gmail.com')
            ->subject('Scheduled Email')
            ->text('This email is sent once a week.');

        $this->mailer->send($email);

        $output->writeln('Email sent successfully!');

        return Command::SUCCESS;
    }
}
