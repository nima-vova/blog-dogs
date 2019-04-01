<?php


use Phinx\Migration\AbstractMigration;

class MyMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        //        CREATE TABLE `roles` (
        //    `id` int(10) NOT NULL,
        //  `name` varchar(20) DEFAULT NULL
        //) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('roles', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('name', 'string', ['limit' => 20, 'null' => FALSE])
            ->create();

        //        CREATE TABLE `users` (
        //    `id` int(10) NOT NULL,
        //  `login` varchar(20) NOT NULL,
        //  `password` varchar(150) NOT NULL,
        //  `token` varchar(150) NOT NULL,
        //  `user_agent` varchar(150) NOT NULL,
        //  `first_name` varchar(20) NOT NULL,
        //  `last_name` varchar(20) NOT NULL,
        //  `role_id` int(10) NOT NULL,
        //  `dt_of_regist` date DEFAULT NULL
        //) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('users', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('login', 'string', ['limit' => 20, 'null' => FALSE])
            ->addColumn('password', 'text', ['limit' => 150, 'null' => FALSE])
            ->addColumn('token', 'text', ['limit' => 150, 'null' => FALSE])
            ->addColumn('user_agent', 'text', ['limit' => 150, 'null' => FALSE])
            ->addColumn('first_name', 'string', ['limit' => 20, 'null' => FALSE])
            ->addColumn('last_name', 'string', ['limit' => 20, 'null' => FALSE])
            ->addColumn('role_id', 'integer', ['limit' => 10, 'null' => FALSE])
            ->addColumn('dt_of_regist', 'date', ['null' => TRUE])
            ->create();

        //        CREATE TABLE `tags` (
        //    `id` int(10) NOT NULL,
        //  `name` varchar(20) NOT NULL
        //) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('tags', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('name', 'string', ['limit' => 20, 'null' => FALSE])
            ->create();

        //          CREATE TABLE `publications` (
        //       `id` int(10) NOT NULL,
        //       `name` mediumtext,
        //       `full_text` mediumtext,
        //       `image` mediumtext,
        //       `dt_of_pub` date DEFAULT NULL
        //       ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('publications', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('name', 'text', ['limit' => 200, 'null' => FALSE])
            ->addColumn('full_text', 'text')
            ->addColumn('image', 'text')
            ->addColumn('dt_of_pub', 'date', ['null' => TRUE])
            ->create();

        //        CREATE TABLE `publications_users` (
        //    `id` int(10) NOT NULL,
        //  `user_id` int(10) NOT NULL,
        //  `publication_id` int(10) NOT NULL
        //) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('publications_users', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('user_id', 'integer', ['limit' => 10, 'null' => FALSE])
            ->addColumn('publication_id', 'integer', ['limit' => 10, 'null' => FALSE])
            ->create();

        //        CREATE TABLE `tags_publications` (
        //    `id` int(10) NOT NULL,
        //  `publication_id` int(10) NOT NULL,
        //  `tag_id` int(10) NOT NULL
        //) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('tags_publications', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('publication_id', 'integer', ['limit' => 10, 'null' => FALSE])
            ->addColumn('tag_id', 'integer', ['limit' => 10, 'null' => FALSE])
            ->create();

        //        CREATE TABLE `comments` (
        //    `id` int(10) NOT NULL,
        //  `user_id` int(10) NOT NULL,
        //  `publication_id` int(10) NOT NULL,
        //  `dt_of_pub` date DEFAULT NULL
        //) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('comments', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('user_id', 'integer', array('limit' => 10, 'null' => FALSE))
            ->addColumn('publication_id', 'integer', array('limit' => 10, 'null' => FALSE))
            ->addColumn('dt_of_pub', 'date', ['null' => TRUE])
            ->create();

        //        CREATE TABLE `contacts` (
        //       `id` int(11) NOT NULL,
        //       `tel` varchar(20) NOT NULL,
        //       `email` varchar(50) NOT NULL,
        //       `address` varchar(200) NOT NULL,
        //       `description` text
        //        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        //id - autoincrement field is created automatically (read documentation phinx)
        $table = $this->table('contacts', ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8']);
        $table->addColumn('tel', 'string', ['limit' => 20, 'null' => FALSE])
            ->addColumn('email', 'string', ['limit' => 50, 'null' => FALSE])
            ->addColumn('address', 'text', ['limit' => 200, 'null' => FALSE])
            ->addColumn('description', 'text', ['null' => FALSE])
            ->create();

        //    --
        //Table foreign key constraints `comments`
        //    --
        $this->execute('ALTER TABLE `comments`
            ADD CONSTRAINT `fk_publication_id` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON UPDATE CASCADE,
            ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE');

        //    --
        // Table foreign key constraints `publications_users`');
        //    --
        $this->execute('ALTER TABLE `publications_users`
            ADD CONSTRAINT `fk_pub_publication_id` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `fk_pub_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)');

        //--
        //-- Table foreign key constraints `tags_publications`
        //    --
        $this->execute('ALTER TABLE `tags_publications`
            ADD CONSTRAINT `fk_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) 
            ON DELETE CASCADE ON UPDATE CASCADE');

        //--
        //-- Table foreign key constraints `users`
        //    --
        $this->execute('ALTER TABLE `users`
            ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)');
    }
}