<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;


class SubscriptionCommand extends Command
{
    protected static $defaultName = 'app:subscription';

    private $em;
    private $mailer;
    private $router;
    private $appDomain;

    /**
     * SubscriptionCommand constructor.
     * @param EntityManagerInterface $em
     * @param MailerInterface $mailer
     * @param RouterInterface $router
     * @param $appDomain
     */
    public function __construct(EntityManagerInterface $em, MailerInterface $mailer, RouterInterface $router, $appDomain)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
        $this->mailer=$mailer;
        $this->router=$router;
        $this->appDomain=$appDomain;
    }


    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $io = new SymfonyStyle($input, $output);
        $articles=$this->getFreshArticles();
        $users=$this->em->getRepository(User::class)->findBy(['isSubscribed' => 1]);
//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        foreach ($users as $user) {
            $email = (new TemplatedEmail())
                ->from('no-reply@symfony-blog.com.ua')
                ->to(new Address($user->getEmail(), 'Beetroot'))
                ->subject('Новая рассылка')

                // path of the Twig template to render
                ->htmlTemplate('emails/mailing.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'articles' => $articles,
                    'app_domain'=>$this->appDomain,
                    'unsubscribe_url' => $this->router->generate(
                        'app_unsubscribe',
                        ['id' => $user->getId()]
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL

                ]);
            $this->mailer->send($email);
        }
        return $email;
//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }


    }

    /**
     * @param int $limit
     * @return mixed
     */
    private function getFreshArticles($limit = 10)
    {
        return $articles = $this->em->createQueryBuilder()
            ->from(Article::class, 'a')
            ->select('a')
            ->orderBy('a.createdAt', 'desc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}
