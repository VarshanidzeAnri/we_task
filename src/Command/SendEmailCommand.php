<?php

namespace App\Command;

use App\Entity\News;
use App\Entity\NewsView;
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
    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Send email with top 10 viewed news weekly');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startOfWeek = new \DateTimeImmutable('monday this week');
        $endOfWeek = new \DateTimeImmutable('sunday this week');
        
        $topNews = $this->getTopViewedNews();
        $emailContent = $this->formatEmailContent($topNews);
        
        $email = (new Email())
            ->from('varshanadev@gmail.com')
            ->to('anrivarshanidze2407@gmail.com')
            ->subject('Top 10 News Views: ' . $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d'))
            ->text($emailContent);

        $this->mailer->send($email);

        $output->writeln('Email with top 10 viewed news sent successfully!');

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
