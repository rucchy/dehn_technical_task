[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg?style=flat-square)](https://php.net/)

# Dehn technical Task

Code written by Chema Ruano Fernández for Dehn Digital Solution.

## Description
This code is for the technical interview of Dehn Digital Solution. The objective is to develop a simple command-line task management
application. The application should allow users to create, list,
update, and delete tasks.

1. Users should be able to create a new task by providing a task title, description, and a due date.
2. Users should be able to list all the tasks in tabular form with columns for the task ID, title, description, due date, and status (i.e.,
   completed or pending).
3. Users should be able to update the task's title, description, due date, or mark it as completed by providing the task ID.
4. Users should be able to delete a task by providing the task ID.
5. The data must be stored on a simple flat file (e.g., JSON, CSV, or XML) for persistence

## Requirements

    * docker
    * docker-compose
    
If you have the command `make` available, there is a Makefile to set up the project, run the tests and access to the 
internal shell of the docker.

## How to run
With command `make init` you will build and run the docker, execute the composer install, create a necessary file and enter in the docker's shell.
If you can't use the `make` then exec the next commands:

    docker-compose up -d --build
	docker-compose exec cli sh -c "cd dehn_technical_task/cli && composer install"
    docker-compose exec cli sh -c "echo '{}' > dehn_technical_task/cli/files/json/task.json"
	docker-compose exec --user 1000 cli bash -l

Once inside the shell, you have to move to the project folder and the cli folder:

    cd dehn_technical_task/cli

And here you can use the command `php bin/console` to see all the help of the CLI app.

Of course all the commands can be executed outside the docker in this way e.g.:

    docker-compose exec cli sh -c "cd dehn_technical_task/cli && php bin/console app:create-task title description"

And if you have the PHP 8.2 installed in your system, you can execute them like inside the docker shell, but you have to 
execute the `composer install` and create the file `task.json` in `dehn_technical_task\cli\files\json`

The four commands that are required for this technical task are:

    php bin/console app:create-task  
    php bin/console app:delete-task  
    php bin/console app:list-tasks  
    php bin/console app:update-task  

For all of them, you can pass the parameter `-h` to see the help of that command e.g.:

    php bin/console app:create-task -h

Now I'm going to explain the parameters for each command:

### Create Task Command
      php bin/console app:create-task title description dueDate

Every params are required but there is a wizard if you miss someone.
* The `title` and `description` could be anything, and it would be a string.
* The `dueDate` represent a date and can't be earlier than today. Also had to be a string with this format: `Y-m-d` 
e.g. 2023-10-31

Full e.g. `php bin/console app:create-task "Task Title" "Description of the task" 2023-10-31`

### List Task Command
      php bin/console app:list-tasks

Right now, no params are available for this command but a improvement could be some parameters to paginate the results.

### Update Task Command
      php bin/console app:update-task id --title="New title" --description="New description" --dueDate=2023-10-10 --completed

The options of the commands are (of course) optionals but if you don't pass any, a wizards will help you. 
If you pass some of them, it will update these fields in the task.
* The param `id` is required but there is a wizard if you miss it.
* The `--title` and `--description` could be anything, and it would be a string.
* The `--dueDate` represent a date and can't be earlier than today. Also had to be a string with this format: `Y-m-d`
    e.g. 2023-10-31
* The `--completed` will change the status of the task to completed.

### Delete Task Command
      php bin/console app:delete-task id

* The param `id` is required but there is a wizard if you miss it.


## Test
Unfortunately, I don't have too much experience with testing (I know, I shouldn't say this to my evaluators, but I prefer
to be honest) so, I did a Unit test for the validator, but I'm not sure how to do the rest of the tests. For testing the
other class I need data o create data, and it would be bad (I suppose if it had a DB is not good to write on it).

At the time I'm writing this, I have realized that I could write another JSON file for the tests and change between 
prod and test environment, so it would be an improvement.

To run the tests you can do it with one of these options:

    * make tests
    * docker-compose exec cli ./dehn_technical_task/cli/vendor/bin/phpunit dehn_technical_task/cli/tests
    * go inside the docker's shell and go to the cli folder and exec ./vendor/bin/phpunit tests

## Design Decisions
1. **Docker**. It is easier to deploy and run the app because you don't need to
install weird things that maybe you won't use anymore like a PHP version.

2. **PHP**. It's the language that I usually use and it's the one I know best, also the Symfony Console 
Component that I use it is for PHP.

3. **Composer**. For manage the PHP component is only one I know.

4. **Symfony Console Component**. It's a component of Symfony that accelerates development with cool things 
like the styles of the console and functionality for validate/ask parameters and options among other things.

5. **JSON**. I prefer the JSON files and I think it's better in this case because you can have an index (the ID of the
task) so it's better to retrieve the data.

6. **Structure of the project**. I make this organization of classes because I split and group the functionality:
   - `bin` where the main entrance of the system is, the executable.
   - `files` where we storage the data. There is a subfolder `json` but it could be other for `xml` or `csv`.
   - `src` where the codes goes. The `App` folder to encapsulate the app functionality
     - `Command` where the logic of the commands goes. There is a BaseCommand to don't repeat some of the logic that all
     commands use. There is a `Task` folder because in the future the CLI could be use for more things and have other 
     entities
     - `Entity` where the model logic goes. There is a `EntityInterface` to make sure we have some of the methods that 
     we need for the EntityManager. The `TaskState` entity is created to scale the app and add some new states of the tasks.
     Probably, with a DB, I would create it to have its own table.
     - `Repository` where the "query" logic goes. There is an interface to implement all the possible future repositories.
     - `Utils` I put the `entityManager` logic here. Usually we have a DB and I would use the `Doctrine` component to manage
     the data/entities. We don't have, so I create one with an interface. In this way we can create different `entityManager`
     for the different ways we can store the data (JSON, XML, CSV) and change that with affect the rest of the code, it's
     more scalable.
     - `Validator` where the validator logic goes.
   - `tests` where the tests goes. Sadly ony have one unit tests for the validator.
   - `vendor` where the external component goes. Usually this folder is on the gitignore but one of the things in the 
   submission part of the technical task said that "and any supporting
     files (e.g., build files, dependencies).", so I let here.

7. **EntityManager**. I have some doubts about this. I think there are some ways to resolve the "save" data problem. One
of them is the make an `objectModel` with an abstract class and extend all the entities from it. In this way you can have 
some entity methods to save/delete but the logic of store is mixed with the model logic and I don't like it. With the 
`entityManager` is more flexible and don't mix the logics. Also, Symfony/Doctrine works in this way so maybe it could be
easier to change to something with DB.

## Final thoughts
I enjoy work in this task. I want to know about your feedback around this to improve.

Thank you for your time, and sorry for my English (I want to take some lessons because I know it's not the best)
