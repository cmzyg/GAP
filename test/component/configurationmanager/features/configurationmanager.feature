Feature: configuration manager
  In order to be able to use the services of the gap system
  As the configuration manager i will need to provide the gap system with the details
  of a licensee and the current server on which the current request is
  associated with and only then can the gap system will be able to provide the necessary services
  requested from the system. Part of my Job will also be to store the details of each licensee and server
  in a way that will be easy for me to read them back without so much cost associated with this read and write

  Scenario: Get the details of a licensee by the id
    Given that i have the licensee id "85"
    When i run getConfiguration by licensee
    Then i should get the licensee details as an instance of "\model\gmapi\GmapiLicensee"


  Scenario: Get the details of a licensee by its operator name
    Given that i have "DummyAPI" as an operator name of the licensee
    When i run getConfiguration by operator
    Then i should get the licensee details as an instance of "\model\gmapi\GmapiLicensee"

  Scenario Outline: Get the details of the current server
    Given that the name of the current server is "<server_name>"
    And i have the following "<keys>" as part of the server configuration
    When i run getServerConfiguration
    Then i should get an array that contains "<key_value>"

  Examples:
    | server_name   | keys              | key_value                           |
    |  MALTA_DEVEL  | ERROR_LOG_PATH    | /app_logs/php_error_logs/           |
    |  MALTA_DEVEL  | 	API_URL       | http://gmapi-devel.isoftbet.com/    |


  Scenario Outline: Store licensee details that was retrieved from database
    Given that i have the following licensee id "<id>"
    Given that i have not fetched the details of this licensee with this id before
    When i run fetchAndStoreLicenseeDetails
    Then i should have a file created with the name "<id>.json" and get an instance of "\model\gmapi\GmapiLicensee"

  Examples:
    | id  |
    |  85 |
    |  51 |
    |  77 |