<?php

namespace Gibbon\Install;

use Gibbon\Install\Config;
use Twig\Environment;

/**
 * Gibbon installer object / method collection. For writing installer
 * of Gibbon for different situation (e.g. web, cli).
 *
 * @version v23
 * @since   v23
 *
 */
class Installer
{

    /**
     * Twig template engine to use for config file rendering.
     *
     * @var \Twig\Environment
     */
    protected $templateEngine;

    /**
     * A PDO connection to database server.
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * Constructor
     *
     * @version v23
     * @since   v23
     *
     * @param string      $installPath     The system path for installing Gibbon.
     * @param Environment $templateEngine  The template engine to generate config file from.
     */
    public function __construct(
        Environment $templateEngine
    ) {
        $this->templateEngine = $templateEngine;
    }

    /**
     * Generate configuration file from twig template.
     *
     * @version v23
     * @since   v23
     *
     * @param Config $config    The config object to generate file from.
     * @param string $path      The full path (includes filename) to generate.
     * @param string $template  The template file to use.
     *
     * @return self
     */
    public function createConfigFile(
        Context $context,
        Config $config,
        string $template = 'installer/config.twig.html'
    )
    {
        $contents = $this->templateEngine->render(
            $template,
            static::processConfigVars($config->getVars())
        );

        // Write config contents
        $fp = fopen($context->getConfigPath(), 'wb');
        fwrite($fp, $contents);
        fclose($fp);

        if (!file_exists($context->getConfigPath())) { //Something went wrong, config.php could not be created.
            throw new \Exception(__('../config.php could not be created.'));
        }
        return $this;
    }

    /**
     * Set the internal connection for database operations.
     *
     * @version v23
     * @since   v23
     *
     * @param \PDO $connection
     *
     * @return self
     */
    public function setConnection(\PDO $connection): Installer
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Create a user from data in the given assoc array.
     *
     * @version v23
     * @since   v23
     *
     * @param array $user
     *
     * @return bool  True on success or fail on failure.
     *
     * @throws \PDOException On error if PDO::ERRMODE_EXCEPTION option is set to true
     *                       in the instance's \PDO connection.
     */
    public function createUser(array $user): bool
    {
        // TODO: add some default values to $user in case any field(s) is / are missed.
        $statement = $this->connection->prepare('INSERT INTO gibbonPerson SET
            gibbonPersonID=1,
            title=:title,
            surname=:surname,
            firstName=:firstName,
            preferredName=:preferredName,
            officialName=:officialName,
            username=:username,
            password="",
            passwordStrong=:passwordStrong,
            passwordStrongSalt=:passwordStrongSalt,
            status=:status,
            canLogin=:canLogin,
            passwordForceReset=:passwordForceReset,
            gibbonRoleIDPrimary=:gibbonRoleIDPrimary,
            gibbonRoleIDAll=:gibbonRoleIDAll,
            email=:email'
        );
        return $statement->execute($user);
    }

    /**
     * Set a user of given gibbonPersonID as staff.
     *
     * @version v23
     * @since   v23
     *
     * @param int $gibbonPersonID  The ID of the user.
     * @param string $type         Optional. The type of user. Default 'Teaching'.
     */
    public function setPersonAsStaff(int $gibbonPersonID, string $type = 'Teaching')
    {
        $statement = $this->connection->prepare('INSERT INTO gibbonStaff SET gibbonPersonID=:gibbonPersonID, type=:type');
        return $statement->execute([
            'gibbonPersonID' => $gibbonPersonID,
            'type' => $type,
        ]);
    }

    /**
     * Set a certain setting to the value.
     *
     * @version v23
     * @since   v23
     *
     * @param string $name             The name of the setting.
     * @param string $value            The value of the setting.
     * @param string $scope            Optional. The scope of the setting. Default 'System'.
     * @param boolean $throw_on_error  Throw exception when encountered one in database query. Default false.
     *
     * @return boolean  True on success, or false on failure.
     */
    public function setSetting(string $name, string $value, string $scope = 'System', bool $throw_on_error=false): bool {
        if ($throw_on_error) {
            $statement = $this->connection->prepare('UPDATE gibbonSetting SET value=:value WHERE scope=:scope AND name=:name');
            return $statement->execute([':scope' => $scope, ':name' => $name, ':value' => $value]);
        }
        try {
            $statement = $this->connection->prepare('UPDATE gibbonSetting SET value=:value WHERE scope=:scope AND name=:name');
            return $statement->execute([':scope' => $scope, ':name' => $name, ':value' => $value]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Process config variables into string literals stored in string.
     *
     * @version v23
     * @since   v23
     *
     * @param array variables
     *      An array of config variables to be passed into config
     *      file template.
     *
     * @return array
     *      The variables forced to be string type and properly quoted.
     */
    public static function processConfigVars(array $variables)
    {
        $variables += [
            'databaseServer' => '',
            'databaseUsername' => '',
            'databasePassword' => '',
            'databaseName' => '',
            'guid' => '',
        ];
        return array_map(function ($value) {
            return var_export((string) $value, true); // force render into string literals
        }, $variables);
    }

    /**
     * Remove remarks (e.g. comments) from SQL string.
     *
     * @version v23
     * @since   v23
     *
     * @param string &$sql  The SQL string to process. Will be emptied
     *                      after run to save memory.
     *
     * @return string The resulted SQL string.
     */
    public static function removeSqlRemarks(string &$sql): string
    {
        $lines = explode("\n", $sql);

        // save memory.
        $sql = '';

        $linecount = count($lines);
        $output = '';

        for ($i = 0; $i < $linecount; ++$i) {
            if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) {
                if (isset($lines[$i][0]) && $lines[$i][0] != '#') {
                    $output .= $lines[$i]."\n";
                } else {
                    $output .= "\n";
                }
                // Trading a bit of speed for lower mem. use here.
                $lines[$i] = '';
            }
        }

        return $output;
    }


    /**
     * Split a very long SQL string (with multiple statements) into iterable
     * of the individual SQL statements. Based on split_sql_file() of previous
     * Gibbon versions.
     *
     * Expects trim() to have already been run on query string $sql.
     *
     * @version v23
     * @since   v23
     *
     * @param string &$sql        The SQL string to process. Will be emptied
     *                            after run to save memory.
     * @param string $terminator  The terminator string at the end of each statement. Default ';'
     *
     * @return iterable
     */
    public static function splitSql(string &$sql, string $terminator = ';'): iterable
    {
        // Split up our string into "possible" SQL statements.
        $tokens = explode($terminator, $sql);

        // save memory.
        $sql = '';

        /**
         * @var string[]
         */
        $output = [];

        // we don't actually care about the matches preg gives us.
        $matches = array();

        // this is faster than calling count($oktens) every time thru the loop.
        $token_count = count($tokens);
        for ($i = 0; $i < $token_count; ++$i) {
            // Don't wanna add an empty string as the last thing in the array.
            if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
                // This is the total number of single quotes in the token.
                $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
                // Counts single quotes that are preceded by an odd number of backslashes,
                // which means they're escaped quotes.
                $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

                $unescaped_quotes = $total_quotes - $escaped_quotes;

                // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
                if (($unescaped_quotes % 2) == 0) {
                    // It's a complete sql statement.
                    $output[] = $tokens[$i];
                    // save memory.
                    $tokens[$i] = '';
                } else {
                    // incomplete sql statement. keep adding tokens until we have a complete one.
                    // $temp will hold what we have so far.
                    $temp = $tokens[$i].$terminator;
                    // save memory..
                    $tokens[$i] = '';

                    // Do we have a complete statement yet?
                    $complete_stmt = false;

                    for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); ++$j) {
                        // This is the total number of single quotes in the token.
                        $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                        // Counts single quotes that are preceded by an odd number of backslashes,
                        // which means they're escaped quotes.
                        $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

                        $unescaped_quotes = $total_quotes - $escaped_quotes;

                        if (($unescaped_quotes % 2) == 1) {
                            // odd number of unescaped quotes. In combination with the previous incomplete
                        // statement(s), we now have a complete statement. (2 odds always make an even)
                        $output[] = $temp.$tokens[$j];

                        // save memory.
                        $tokens[$j] = '';
                            $temp = '';

                        // exit the loop.
                        $complete_stmt = true;
                        // make sure the outer loop continues at the right point.
                        $i = $j;
                        } else {
                            // even number of unescaped quotes. We still don't have a complete statement.
                        // (1 odd and 1 even always make an odd)
                        $temp .= $tokens[$j].$terminator;
                        // save memory.
                        $tokens[$j] = '';
                        }
                    } // for..
                } // else
            }
        }

        return $output;
    }
}
