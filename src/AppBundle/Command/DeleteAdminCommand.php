<?php

namespace AppBundle\Command;

use AppBundle\Entity\Admin;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * A command console that deletes admins from the database.
 *
 * $ php bin/console app:delete-admin
 *
 * @subpackage Command
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class DeleteAdminCommand extends ContainerAwareCommand
{
    const MAX_ATTEMPTS = 5;

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
            ->setName('app:delete-admin')
            ->setDescription('Deletes admins from the database')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of an existing admin')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command deletes admins from the database:

  <info>php %command.full_name%</info> <comment>username</comment>

If you omit the argument, the command will ask you to
provide the missing value:

  <info>php %command.full_name%</info>
HELP
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        if ($username !== null) {
            return;
        }

        $output->writeln([
            '',
            'Delete User Command Interactive Wizard',
            '-----------------------------------',
            '',
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:delete-user username',
            '',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);

        $helper = $this->getHelper('question');

        $question = new Question(' > <info>Username</info>: ');
        $question->setValidator([$this, 'usernameValidator']);
        $question->setMaxAttempts(self::MAX_ATTEMPTS);

        $username = $helper->ask($input, $output, $question);
        $input->setArgument('username', $username);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');

        $repository = $this->entityManager->getRepository(Admin::class);
        $admin = $repository->findOneByUsername($username);

        if ($admin === null) {
            throw new \RuntimeException(sprintf('User with username "%s" not found.', $username));
        }

        $adminId = $admin->getId();

        $this->entityManager->remove($admin);
        $this->entityManager->flush();

        $output->writeln([
            '',
            sprintf('[OK] User "%s" (ID: %d) was successfully deleted.', $admin->getUsername(), $adminId),
        ]);
    }
}
