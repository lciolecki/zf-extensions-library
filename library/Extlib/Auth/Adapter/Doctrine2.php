<?php

namespace Extlib\Auth\Adapter;

/**
 * Auth adapter for Doctrine v 2.*
 *
 * @TODO getAmbiguityIdentity()
 *
 * @category        Extlib
 * @package         Extlib\Auth
 * @subpackage      Extlib\Auth\Adapter
 * @author          Loïc Frering <loic.frering@gmail.com> (https://github.com/loicfrering/losolib/blob/master/src/LoSo/Zend/Auth/Adapter/Doctrine2.php)
 * @modification    Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 */
class Doctrine2 implements \Zend_Auth_Adapter_Interface
{
    /**
     * Doctrine EntityManager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * The entity name to check for an identity.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Identity string
     *
     * @var string
     */
    protected $identity;

    /**
     * Column to be used as identity.
     *
     * @var string
     */
    protected $identityColumn;

    /**
     * Credential string
     *
     * @var string
     */
    protected $credential;

    /**
     * The column to be used as credential.
     *
     * @var string
     */
    protected $credentialColumn;

    /**
     * Treatment applied to the credential, such as MD5() or PASSWORD()
     *
     * @var string
     */
    protected $credentialTreatment = null;

    /**
     * Additional conditions by: field => value
     *
     * @var array
     */
    protected $conditions = array();

    /**
     * Array of authentication result information
     *
     * @var array
     */
    protected $authenticateResultInfo = null;

    /**
     * Flag to indicate same Identity can be used with
     * different credentials. Default is FALSE and need to be set to true to
     * allow ambiguity usage.
     *
     *
     * @var boolean
     */
    protected $ambiguityIdentity = false;

    /**
     * Constructor sets configuration options.
     *
     * @param \Doctrine\ORM\EntiyManager
     * @param string
     * @param string
     * @param string
     * @return void
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, $entityName = null, $identityColumn = null, $credentialColumn = null)
    {
        $this->em = $em;

        if (null !== $entityName) {
            $this->setEntityName($entityName);
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }
    }

    /**
     * Sets a flag for usage of identical identities
     * with unique credentials. It accepts integers (0, 1) or boolean (true,
     * false) parameters. Default is false.
     *
     * @param  int|bool $flag
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setAmbiguityIdentity($flag)
    {
        if (is_integer($flag)) {
            $this->ambiguityIdentity = (1 === $flag ? true : false);
        } elseif (is_bool($flag)) {
            $this->ambiguityIdentity = $flag;
        }
        return $this;
    }

    /**
     * Returns TRUE for usage of multiple identical
     * identies with different credentials, FALSE if not used.
     *
     * @return bool
     */
    public function getAmbiguityIdentity()
    {
        return $this->ambiguityIdentity;
    }

    /**
     * Return EnityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * Set EnityManager
     *
     * @param \Doctrine\ORM\EntityManager $em
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setEm(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * Set entity name.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
        return $this;
    }

    /**
     * Set identity column.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setIdentityColumn($identityColumn)
    {
        $this->identityColumn = $identityColumn;
        return $this;
    }

    /**
     * Return identity column name
     *
     * @return string
     */
    public function getIdentityColumn()
    {
        return $this->identityColumn;
    }

    /**
     * Set credential column.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setCredentialColumn($credentialColumn)
    {
        $this->credentialColumn = $credentialColumn;
        return $this;
    }

    /**
     * Return credential column name
     *
     * @return string
     */
    public function getCredentialColumn()
    {
        return $this->credentialColumn;
    }

    /**
     * Set the value to be used as identity.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

    /**
     * Return identity string
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Set the value to be used as credential.
     *
     * @param string
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }

    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Get addionals conditions
     *
     * @return array
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Sset addionals conditions
     *
     * @param array $conditions
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }


    /**
     * Add conditions, merge existings with new
     *
     * @param array $add
     */
    public function addConditions(array $add)
    {
        $this->conditions = array_merge($this->conditions, $add);
        return $this;
    }

    /**
     * setCredentialTreatment() - allows the developer to pass a parameterized string that is
     * used to transform or treat the input credential data
     *
     * In many cases, passwords and other sensitive data are encrypted, hashed, encoded,
     * obscured, or otherwise treated through some function or algorithm. By specifying a
     * parameterized treatment string with this method, a developer may apply arbitrary SQL
     * upon input credential data.
     *
     * Examples:
     *
     *  'PASSWORD(?)'
     *  'MD5(?)'
     *
     * @param  string $treatment
     * @return \Extlib\Auth\Adapter\Doctrine2
     */
    public function setCredentialTreatment($treatment)
    {
        $this->credentialTreatment = $treatment;
        return $this;
    }

    /**
     * Defined by Zend_Auth_Adapter_Interface. This method is called to
     * attempt an authentication. Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws \Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return \Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        $dqlQuery = $this->_authenticateCreateSelect();
        $resultIdentities = $this->_authenticateQueryDql($dqlQuery);

        if (($authResult = $this->_authenticateValidateResultset($resultIdentities)) instanceof \Zend_Auth_Result) {
            return $authResult;
        }

        if (true === $this->getAmbiguityIdentity()) {
            $validIdentities = array();
            foreach ($resultIdentities as $identity) {
                if (1 === (int) $identity['zend_auth_credential_match']) {
                    $validIdentities[] = $identity;
                }
            }
            $resultIdentities = $validIdentities;
        }

        $authResult = $this->_authenticateValidateResult(array_shift($resultIdentities));
        return $authResult;
    }

    /**
     * This method abstracts the steps involved with
     * making sure that this adapter was indeed setup properly with all
     * required pieces of information.
     *
     * @throws \Zend_Auth_Adapter_Exception - in the event that setup was not done properly
     */
    protected function _authenticateSetup()
    {
        $exception = null;

        if (null === $this->em || !$this->em instanceof \Doctrine\ORM\EntityManager) {
            $exception = 'A Doctrine2 EntityManager must be supplied for the Extlib\Auth\Adapter\Doctrine2 authentication adapter.';
        } elseif(empty($this->entityName)){
            $exception = 'A entityName must be supplied for the Extlib\Auth\Adapter\Doctrine2 authentication adapter.';
        } elseif (empty($this->identityColumn)) {
            $exception = 'An identity field must be supplied for the Extlib\Auth\Adapter\Doctrine2 authentication adapter.';
        } elseif (empty($this->credentialColumn)) {
            $exception = 'A credential field must be supplied for the Extlib\Auth\Adapter\Doctrine2 authentication adapter.';
        } elseif (empty($this->identity)) {
            $exception = 'A value for the identity was not provided prior to authentication with Extlib\Auth\Adapter\Doctrine2.';
        } elseif (empty($this->credential)) {
            $exception = 'A credential value was not provided prior to authentication with Extlib\Auth\Adapter\Doctrine2.';
        }

        if (null !== $exception) {
            throw new \Zend_Auth_Adapter_Exception($exception);
        }

        $this->authenticateResultInfo = array(
            'code'      =>  \Zend_Auth_Result::FAILURE,
            'identity'  =>  $this->getIdentity(),
            'messages'  =>  array()
        );

        return true;
    }

    /**
     * Construct the Doctrine query.
     *
     * @return \Doctrine\ORM\Query
     */
    protected function _authenticateCreateSelect()
    {
        if ($this->credentialTreatment === null) {
            $this->credentialTreatment = '?';
        }

        $select = sprintf(
            '(CASE WHEN e.%s = %s THEN 1 ELSE 0 END) as zend_auth_credential_match',
            $this->credentialColumn,
            str_replace('?', $this->getEm()->getConnection()->quote($this->credential), $this->credentialTreatment)
        );

        $qb = $this->em->createQueryBuilder()
                       ->select($select)
                       ->from($this->entityName, 'e')
                       ->where('e.' . $this->identityColumn . ' = :identity');

        $parameters = array('identity' => $this->getIdentity());
        foreach ($this->conditions as $condition => $params) {
            $qb->andWhere('e.' . $condition);
            $parameters = array_merge($parameters, $params);
        }

        $qb->setParameters($parameters);

        return $qb->getQuery();
    }

    /**
     * Get result identities
     *
     * @param \Doctrine\ORM\Query $dqlQuery
     * @return array
     * @throws \Zend_Auth_Adapter_Exception
     */
    protected function _authenticateQueryDql(\Doctrine\ORM\Query $dqlQuery)
    {
        try {
            $resultIdentities = $dqlQuery->getResult();
        } catch (\Exception $e) {
            throw new \Zend_Auth_Adapter_Exception('The supplied parameters to Extlib\Auth\Adapter\Doctrine2 failed to '
                . 'produce a valid sql statement, please check table and column names '
                . 'for validity.', 0, $e);
        }

        return $resultIdentities;
    }

    /**
     * This method attempts to make
     * certain that only one record was returned in the resultset
     *
     * @param array $resultIdentities
     * @return true|\Zend_Auth_Result
     */
    protected function _authenticateValidateResultSet(array $resultIdentities)
    {
        if (count($resultIdentities) < 1) {
            $this->authenticateResultInfo['code'] = \Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
            return $this->_authenticateCreateAuthResult();
        } elseif (count($resultIdentities) > 1 && false === $this->getAmbiguityIdentity()) {
            $this->authenticateResultInfo['code'] = \Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
            return $this->_authenticateCreateAuthResult();
        }

        return true;
    }

    /**
     * This method attempts to validate that
     * the record in the resultset is indeed a record that matched the
     * identity provided to this adapter.
     *
     * @param array $resultIdentity
     * @return \Zend_Auth_Result
     */
    protected function _authenticateValidateResult($resultIdentity)
    {
        if ($resultIdentity['zend_auth_credential_match'] != '1') {
            $this->authenticateResultInfo['code'] = \Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $this->authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
            return $this->_authenticateCreateAuthResult();
        }

        unset($resultIdentity['zend_auth_credential_match']);
        $this->_resultRow = $resultIdentity;

        $this->authenticateResultInfo['code'] = \Zend_Auth_Result::SUCCESS;
        $this->authenticateResultInfo['messages'][] = 'Authentication successful.';
        return $this->_authenticateCreateAuthResult();
    }

    /**
     * Creates a Zend_Auth_Result object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return \Zend_Auth_Result
     */
    protected function _authenticateCreateAuthResult()
    {
        return new \Zend_Auth_Result(
            $this->authenticateResultInfo['code'],
            $this->authenticateResultInfo['identity'],
            $this->authenticateResultInfo['messages']
        );
    }
}