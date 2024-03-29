<?php

namespace DoctrineProxies\__CG__\SpecialCharacterSearch\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class SearchGroupItem extends \SpecialCharacterSearch\Entity\SearchGroupItem implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'id', 'owner', 'resourceClass', 'resourceTemplate', 'isPublic', 'created', 'modified', 'values', 'media', 'siteBlockAttachments', 'searchItem', 'searchValues', 'depth', 'parentItem', 'isParent'];
        }

        return ['__isInitialized__', 'id', 'owner', 'resourceClass', 'resourceTemplate', 'isPublic', 'created', 'modified', 'values', 'media', 'siteBlockAttachments', 'searchItem', 'searchValues', 'depth', 'parentItem', 'isParent'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (SearchGroupItem $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getIsParent()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsParent', []);

        return parent::getIsParent();
    }

    /**
     * {@inheritDoc}
     */
    public function getParentItem()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getParentItem', []);

        return parent::getParentItem();
    }

    /**
     * {@inheritDoc}
     */
    public function getDepth()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDepth', []);

        return parent::getDepth();
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchValues()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSearchValues', []);

        return parent::getSearchValues();
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResourceName', []);

        return parent::getResourceName();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getMedia()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMedia', []);

        return parent::getMedia();
    }

    /**
     * {@inheritDoc}
     */
    public function getSiteBlockAttachments()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSiteBlockAttachments', []);

        return parent::getSiteBlockAttachments();
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchItem()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSearchItem', []);

        return parent::getSearchItem();
    }

    /**
     * {@inheritDoc}
     */
    public function setOwner(\Omeka\Entity\User $owner = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOwner', [$owner]);

        return parent::setOwner($owner);
    }

    /**
     * {@inheritDoc}
     */
    public function getOwner()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOwner', []);

        return parent::getOwner();
    }

    /**
     * {@inheritDoc}
     */
    public function setResourceClass(\Omeka\Entity\ResourceClass $resourceClass = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setResourceClass', [$resourceClass]);

        return parent::setResourceClass($resourceClass);
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceClass()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResourceClass', []);

        return parent::getResourceClass();
    }

    /**
     * {@inheritDoc}
     */
    public function setResourceTemplate(\Omeka\Entity\ResourceTemplate $resourceTemplate = NULL)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setResourceTemplate', [$resourceTemplate]);

        return parent::setResourceTemplate($resourceTemplate);
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceTemplate()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResourceTemplate', []);

        return parent::getResourceTemplate();
    }

    /**
     * {@inheritDoc}
     */
    public function setIsPublic($isPublic)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsPublic', [$isPublic]);

        return parent::setIsPublic($isPublic);
    }

    /**
     * {@inheritDoc}
     */
    public function isPublic()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isPublic', []);

        return parent::isPublic();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreated(\DateTime $dateTime)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreated', [$dateTime]);

        return parent::setCreated($dateTime);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreated()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreated', []);

        return parent::getCreated();
    }

    /**
     * {@inheritDoc}
     */
    public function setModified(\DateTime $dateTime)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setModified', [$dateTime]);

        return parent::setModified($dateTime);
    }

    /**
     * {@inheritDoc}
     */
    public function getModified()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getModified', []);

        return parent::getModified();
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getValues', []);

        return parent::getValues();
    }

    /**
     * {@inheritDoc}
     */
    public function getResourceId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getResourceId', []);

        return parent::getResourceId();
    }

}
