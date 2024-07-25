# Project Guide
 *  This project was created with one functionality, to add Items to a Cart stored in Laravel session, with Database driver.
 * This project does not contain a **UI**, to test the functionality, please refer to the project structure, and the **tests/CheckoutTest.php** file.

# Installlation Requirements

* To get the project up and running you need a Linux VM running on your OS, such as **WSL**, **VirtualBox**, or **Docker** **Desktop**.
* Use **git** to clone the project.
* Make sure the docker engine is installed and run docker **compose up -d**, and that's all that is for it to be working.
* After the first main step, please run **make install** in the project root, if you get a connection refused just wait a few seconds until it works.
* After getting the containers running, and having successfully installed the project, just run the **make test** so you can see all the scenarios running successfully.

<br>

## Project considerations
* The project was developed using [Laravel 11](https://laravel.com/docs/11.x) and [Pest PHP](https://pestphp.com/).
* I only created a single get endpoint, and chose to go with pest to make it easy to test many possible scenarios quickly, or even add new ones.
* **CheckoutDetails.php** Contains the response of every endpint call, that we use in our test assertions.

<br>


## Project structure
### Patterns and practises applied
<ul>
  <li>Dependency Injection/Auto WIring</li>
  <li>SRP</li>
  <li>OCP</li>
  <li>DIP</li>
  <li>Strategy Pattern</li>
  <li>Mediator Pattern</li>
  <li>Bounded Contexts</li>
  <li>State management</li>
</ul>


### Implementation specification
* The main Controller of the project is in **app/Http/Controllers/CartController.php**
* The routes are defined as per laraveling routing in **web.php**.
