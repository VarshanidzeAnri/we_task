<?php

namespace App\Command;

use App\Entity\News;
use App\Entity\NewsView;
use App\Service\EmailService;
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
    name: 'send:mail',
    description: 'Send a email with statistics',
)]
class SendEmailCommand extends Command
{
    private $entityManager;
    private $emailService;

    public function __construct(EntityManagerInterface $entityManager, EmailService $emailService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
    }

    protected function configure()
    {
        $this->setDescription('Send email with top viewed news');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startOfWeek = new \DateTimeImmutable('monday this week');
        $endOfWeek = new \DateTimeImmutable('sunday this week');
        
        $topNews = $this->getTopViewedNews();
        $emailContent = $this->formatEmailContent($topNews);

        $this->emailService->sendMail(
            'varshanadev@gmail.com', 
            'anrivarshanidze2407@gmail.com', 
            'Top 10 News Views: ' . $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d'),
            $emailContent);

        $output->writeln('Email sent successfully!');

        return Command::SUCCESS;
    }
    
    private function getTopViewedNews(): array
    {
        $startOfWeek = new \DateTimeImmutable('monday this week');
        $startOfWeek = $startOfWeek->setTime(0, 0, 0);
        
        $endOfWeek = new \DateTimeImmutable('sunday this week');
        $endOfWeek = $endOfWeek->setTime(23, 59, 59);
        
        $em = $this->entityManager->createQueryBuilder();
        
        return $em->select('n as news', 'COUNT(v.id) as viewCount')
            ->from(News::class, 'n')
            ->join('n.newsViews', 'v')
            ->where('v.created_at BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startOfWeek)
            ->setParameter('endDate', $endOfWeek)
            ->groupBy('n.id')
            ->orderBy('viewCount', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
    
    private function formatEmailContent(array $topNews): string
    {
        $startOfWeek = new \DateTimeImmutable('monday this week');
        $endOfWeek = new \DateTimeImmutable('sunday this week');
        
        $content = "TOP 10 VIEWED NEWS: {$startOfWeek->format('M d')} - {$endOfWeek->format('M d')}\n";
        
        if (empty($topNews)) {
            $content .= "No news views were recorded this week.\n";
            return $content;
        }
        
        foreach ($topNews as $index => $item) {
            $news = $item['news'];
            $viewCount = $item['viewCount'];
            $rank = $index + 1;
            
            $content .= "{$rank}. {$news->getTitle()} - Views: {$viewCount}\n";
        }
                
        return $content;
    }
}
