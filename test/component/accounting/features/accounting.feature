Feature: Accounting
  In order for the gap system to be able to carry out accounting task it will require some accounting
  services from me the accounting component. As an accounting component in the gap system i should be
  able to provide the following services:
  creating a new session for a player, update an existing session,
  create wagers during a session, update wagers during a session,
  create win during a session, update win during
  a session, end a session, check if a session exists,
  get details of a round, check if a round has been settled,
  register values of jackpot contribution and jackpot win.

  Scenario Outline: create a new session
    Given that i have a "<licensee_id>" "<player_id>" "<session_id>" "<game_id>" "<game_skin_id>" "<currency>" "<username>" "<country>"
    And there is no session with this session id
    When i run createNewSession with player "<player_balance>"
    Then i should have an open session with "<session_id>" "<player_id>" and "<player_balance>"

  Examples:
    | player_id   | session_id   | game_id | game_skin_id | currency | username | country | licensee_id |player_balance |
    | samuel      | SAIG11MLK007 | 2090    |  1506        | GBP      | samuel   | GB      | 85          |200000.00      |
    | samuel      | TAIG11M2K007 | 2091    |  1507        | GBP      | samuel   | GB      | 85          |300000.00      |
    | samuel      | KAIG11MLK337 | 2093    |  1508        | GBP      | samuel   | GB      | 85          |400000.00      |


  Scenario Outline: create wager for open session
    Given that i have "<session_id>" "<round_id>" "<transaction_id>" "<coin_value>" "<licensee_id>"
    And the session with "<session_id>" is still open
    When i run createWagerForOpenSession with "<player_balance>" and "<wager_amount>"
    Then i should have in the database a transaction with the same "<round_id>" "<transaction_id>" "<coin_value>" "<wager_amount>"


  Examples:
    | session_id    | round_id   | transaction_id | coin_value | wager_amount  | licensee_id |player_balance|
    | SAIG11MLK007  | RD10001111 | TRX001000      | 1          | 40.05         | 85          | 200000.00    |
    | TAIG11M2K007  | RD10002222 | TRX001111      | 1          | 60.05         | 85          | 300000.00    |
    | KAIG11MLK337  | RD10003333 | TRX002222      | 1          | 80.05         | 85          | 400000.00    |


  Scenario Outline: create win for open session
    Given that i have "<session_id>" "<round_id>" "<transaction_id>" "<coin_value>" "<licensee_id>"
    And the session with "<session_id>" is still open
    When i run createWinForOpenSession with "<player_balance>" and "<win_amount>"
    Then i should have in the database a transaction with the same "<round_id>" "<transaction_id>" "<coin_value>" "<win_amount>"

  Examples:
    | session_id    | round_id   | transaction_id | coin_value | win_amount  | licensee_id |player_balance|
    | SAIG11MLK007  | RD10001111 | TRX011WIN      | 1          | 0.115         | 85          | 199959.95  |
    | TAIG11M2K007  | RD10002222 | TRX021WIN      | 1          | 1.025         | 85          | 299939.95  |
    | KAIG11MLK337  | RD10003333 | TRX031WIN      | 1          | 0.025         | 85          | 399919.95  |

  Scenario Outline: end an opened session
    Given that i have a "<licensee_id>" "<player_id>" "<session_id>" "<game_id>" "<game_skin_id>" "<currency>" "<username>" "<country>"
    And the session with "<session_id>" is still open
    When i run endAnOpenSession with "<player_balance>"
    Then i should have a closed session with "<session_id>" "<player_id>" and "<player_balance>"


  Examples:
    | player_id   | session_id   | game_id | game_skin_id | currency | username | country | licensee_id |player_balance  |
    | samuel      | SAIG11MLK007 | 2090    |  1506        | GBP      | samuel   | GB      | 85          |199960.065      |
    | samuel      | TAIG11M2K007 | 2091    |  1507        | GBP      | samuel   | GB      | 85          |299940.975      |
    | samuel      | KAIG11MLK337 | 2093    |  1508        | GBP      | samuel   | GB      | 85          |399919.975      |


  Scenario Outline: create wager for a closed session
    Given that i have "<session_id>" "<round_id>" "<transaction_id>" "<coin_value>" "<licensee_id>"
    And the session with "<session_id>" is closed
    When i run createWagerForOpenSession with "<player_balance>" and "<wager_amount>"
    Then i should not have in the database a transaction with the same "<round_id>" "<transaction_id>" "<coin_value>" "<wager_amount>"


  Examples:
    | session_id    | round_id   | transaction_id | coin_value | wager_amount  | licensee_id |player_balance|
    | SAIG11MLK007  | RD100011BE | TRX0010BE      | 1          | 10.05         | 85          | 199960.065   |
    | TAIG11M2K007  | RD100022BE | TRX0011EB      | 1          | 20.05         | 85          | 299940.975   |
    | KAIG11MLK337  | RD100033BE | TRX0012BE      | 1          | 30.05         | 85          | 399919.975   |


  Scenario Outline: create win for a closed session
    Given that i have "<session_id>" "<round_id>" "<transaction_id>" "<coin_value>" "<licensee_id>"
    And the session with "<session_id>" is closed
    When i run createWinForOpenSession with "<player_balance>" and "<win_amount>"
    Then i should not have in the database a transaction with the same "<round_id>" "<transaction_id>" "<coin_value>" "<win_amount>"


  Examples:
    | session_id    | round_id   | transaction_id | coin_value | win_amount     | licensee_id |player_balance|
    | SAIG11MLK007  | RD100011WI | TRX0010WI      | 1          | 100.05         | 85          | 199960.065   |
    | TAIG11M2K007  | RD100022WI | TRX0011WI      | 1          | 200.05         | 85          | 299940.975   |
    | KAIG11MLK337  | RD100033WI | TRX0012WI      | 1          | 300.05         | 85          | 399919.975   |


  Scenario Outline: create wager with an existing transaction id
    Given that i have "<session_id>" "<round_id>" "<transaction_id>" "<coin_value>" "<licensee_id>"
    When i run createWagerForOpenSession with "<player_balance>" and "<wager_amount>"
    Then i should not have in the database a transaction with the same "<round_id>" "<transaction_id>" "<coin_value>" "<wager_amount>"


  Examples:
    | session_id    | round_id   | transaction_id | coin_value | wager_amount  | licensee_id |player_balance|
    | SAIG11MLK007  | RD100011BET | TRX0010BE      | 1          | 10.05         | 85          | 199960.065   |
    | TAIG11M2K007  | RD100022BET | TRX0011EB      | 1          | 20.05         | 85          | 299940.975   |
    | KAIG11MLK337  | RD100033BET | TRX0012BE      | 1          | 30.05         | 85          | 399919.975   |

  Scenario Outline: create win with an existing transaction id
    Given that i have "<session_id>" "<round_id>" "<transaction_id>" "<coin_value>" "<licensee_id>"
    When i run createWinForOpenSession with "<player_balance>" and "<win_amount>"
    Then i should not have in the database a transaction with the same "<round_id>" "<transaction_id>" "<coin_value>" "<win_amount>"


  Examples:
    | session_id    | round_id   | transaction_id | coin_value | win_amount     | licensee_id |player_balance|
    | SAIG11MLK007  | RD100011WIN | TRX0010WI      | 1          | 100.05         | 85          | 199960.065   |
    | TAIG11M2K007  | RD100022WIN | TRX0011WI      | 1          | 200.05         | 85          | 299940.975   |
    | KAIG11MLK337  | RD100033WIN | TRX0012WI      | 1          | 300.05         | 85          | 399919.975   |