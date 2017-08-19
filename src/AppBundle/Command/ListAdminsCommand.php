<?php

namespace AppBundle\Command;

use AppBundle\Entity\Admin;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command console that lists all the existing admins.
 *
 * $ php bin/console app:list-admins
 *
 * @subpackage Command
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class ListAdminsCommand extends ContainerAwareCommand
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('app:list-admins')
            ->setDescription('Lists all the existing admins')
            ->addOption('max-results', null, InputOption::VALUE_OPTIONAL, 'Limits the number of users listed', 50)
            ->addOption('send-to', null, InputOption::VALUE_OPTIONAL, 'If set, the result is sent to the given email address')
            
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command lists all the admins registered in the application:

  <info>php %command.full_name%</info>

By default the command only displays the 50 most recent admins. Set the number of
results to display with the <comment>--max-results</comment> option:

  <info>php %command.full_name%</info> <comment>--max-results=2000</comment>

In addition to displaying the admin list, you can also send this information to
the email address specified in the <comment>--send-to</comment> option:

  <info>php %command.full_name%</info> <comment>--send-to=fabien@symfony.com</comment>

HELP
            );
    }

    /**
     * This method is executed before the the execute() method. It's main purpose
     * is to initialize the variables used in the rest of the command methods.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $maxResults = $input->getOption('max-results');
        $admins = $this->entityManager->getRepository(Admin::class)->findBy([], ['id' => 'DESC'], $maxResults);
        $adminsAsPlainArrays = array_map(function (Admin $admin) {
            return [
                $admin->getId(), 
                $admin->getUsername(), 
                $admin->getPassword(), 
                implode(', ', $admin->getRoles())
            ];
        }, $admins);

        $bufferedOutput = new BufferedOutput();

        $table = new Table($bufferedOutput);
        $table
            ->setHeaders(['ID', 'Username', 'Password', 'Roles'])
            ->setRows($adminsAsPlainArrays)
        ;
        $table->render();
        $tableContents = $bufferedOutput->fetch();

        if (null !== $email = $input->getOption('send-to')) {
            $this->sendReport($tableContents, $email);
        }

        $output->writeln($tableContents);
    }

    /**
     * Sends the given $contents to the $recipient email address.
     *
     * @param string $contents
     * @param string $recipient
     */
    private function sendReport($contents, $recipient)
    {
        // See http://symfony.com/doc/current/cookbook/email/email.html
        $mailer = $this->getContainer()->get('mailer');

        $message = $mailer->createMessage()
            ->setSubject(sprintf('app:list-admins report (%s)', date('Y-m-d H:i:s')))
            ->setFrom($this->getContainer()->getParameter('app.notifications.email_sender'))
            ->setTo($recipient)
            ->setBody($contents, 'text/plain')
        ;

        $mailer->send($message);
    }
}
