1. to start the project you need to run the bash script in the terminal (enter the command):
   
   ./start_project.sh

2. while running a script you need to respond to the interactive, namely to enter data to work with the database: 
    hostname, user, password

script does:
    creates a database blog_dog;
    creates a file with the data for typing to the bd (which is used in the project to work with bd);
    creates yml file for migration bd;
    migration is triggered by bd (tables and links are created)