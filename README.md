# Project Setup & Usage

## Installation

To set up the project, run the following command:

```sh
make install
```

This will:
- Copy the environment file
- Start the required Docker services
- Install dependencies
- Set up the database structure
- Populate initial data for tv series task

## Running the Project

Start the project with:

```sh
make start
```

Stop the project with:

```sh
make stop
```

## Running Tests

Execute the test suite using:

```sh
make test
```

## Additional Commands

Other commands can be found in the **Makefile**.

---

# Features & How to Use

## 1. Prime Numbers

**Description:** Outputs numbers from 1 to 100 and identifies their multiples. Prime numbers are marked as `[PRIME]`.

**Usage:**
Run the script from the command line:

```sh
php php.cli print-prime-numbers
```

Run the script from the browser(if default .env is not changed):

```
http://localhost:8099/prime-numbers
```


**Main Code Location:** `app/Actions/PrimeNumbersAction.php`

---

## 2. ASCII Array

**Description:** Generates an array of ASCII characters, removes one randomly, and determines the missing character efficiently.

**Usage:**
Run the script:

```sh
php php.cli print-missing-character
```

Run the script from the browser(if default .env is not changed):

```
http://localhost:8099/missing-character
```

**Main Code Location:** `app/Actions/MissingCharacterAction.php`

---

## 3. TV Series Scheduler

**Description:**
- Stores TV series schedules in a MySQL database.
- Determines the next airing time based on the current date/time or a specified input or current date.
- Can filter by TV series title.

**Usage:**
Check the next TV series schedule with 1 argument(optional) - valid date time in the future, 2 argument(optional) - show title string part):

```sh
php php.cli search-tv-series {2025-02-25T20:00:00} {Friends}
```

Run the script from the browser. If the default `.env` is not changed, use:

```
http://localhost:8099/tv-series?air_time=2025-02-15T20:00:00&show_title=Nar
```

### Query Parameters:
- **`air_time`** (optional): A specific date-time (`YYYY-MM-DDTHH:MM:SS`) to check when the next TV series will air.
- **`show_title`** (optional): A partial or full title of a TV series to filter the results.

If no parameters are provided, the script will use the current date-time and return upcoming TV series.


**Main Code Location:** `app/Actions/SearchTvSeriesAction.php`

**Database Setup:** SQL scripts for table creation and data population are located in:

```
app/Console/Commands/CreateABTestStorageTableCommand.php
app/Console/Commands/PopulateTvSeriesCommand.php
```

---

## 4. A/B Testing Implementation

This implementation allows you to run A/B tests on promotional designs within your web application. 
It uses the `exads/ab-test-data` package to dynamically assign designs based on predefined split percentages and user cookies data.

### Features:
- Supports multiple promotions dynamically.
- Redirects users to different promotional designs based on a split percentage.
- Assigns designs consistently using user cookies.
- Provides a fallback promotion if no valid design is found.
- Option to ignore session tracking by clearing the assigned cookie.
- Can support multiple randomizers with possibility to store data in cache or DB.

### How it Works:
The A/B testing logic is integrated into the web application’s request-handling flow.
The system reads user cookies to determine the design assigned to each user.
If no valid design is found for a user, a fallback promotion is provided.
Users can also clear their session to reset the assigned design or to switch to another promotion

- Navigate to promotions redirect page: `http://localhost:8099/promotion-designs/1`
- If promotion is valid user will be redirected to the ab-test page with design data in cookie `http://localhost:8099/ab-design-page`
- In case if user will navigate to `http://localhost:8099/ab-design-page` without cookie or with invalid cookie data user will be redirected to the fallback promotion `http://localhost:8099/promotion-designs/1` where design will be assigned and user will be redirected to the design page.
- If session support enabled user will always get assigned design until: clear cookie, session config will be turned off in server, user will be force assigned with different promotion through `http://localhost:8099/promotion-designs/1` link

### Main Code Locations:
- **Controllers:** `app/Controllers/ABTestDesignRedirectController.php; src/Controllers/GetABDesignPageController.php `
- **Logic Handler:** `app/Services/ABTest/ABTestService.php`
- **Configuration:** `config/ab-test.php`
