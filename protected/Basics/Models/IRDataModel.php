<?php

namespace IRERP\Basics\Models;

use \Doctrine\Common\Annotations\AnnotationReader,
	 \IRERP\Basics\Annotations\scField,
	 \CModel;

//This shoud be defined inside php.ini file, not here
//date_default_timezone_set('UTC');

/**
 * @MappedSuperclass
 * @author masoud
 *
 */
class IRDataModel extends CModel
{
	/**
	 * Entity Manager That This Class Use To 
	 * Doing Something To Objects
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entityManager;
	
	/**
	 * @Id @generatedValue(strategy="AUTO") @Column(type="integer")
	 * @var integer
	 */
	protected $id = null;
	/**
	 * @Column(type="integer")
	 * @version
	 * @var integer
	 */
	protected $version = 0;
	
	/**
	 * @Column(type="datetime")
	 * @var DateTime
	 */
	protected $dateCreated;
	
	/**
	 * @Column(type="datetime")
	 * @var DateTime
	 */
	Protected $dateLastModified;
	
	/**
	 * @Column(type="boolean")
	 * @var boolean
	 */
	protected $IsDeleted=false;
	
	
	/**
	 * @Column(type="integer")
	 * @var integer
	 */
	protected $creatorUserId=-1;
	
	/**
	 * @Column(type="integer")
	 * @var integer
	 */
	protected $modifierUserId=-1;
	
	/**
	 * 
	 * Values of all annotations is stored in this array
	 * @var string[]
	 */
	protected $annotationValues = array();
	
	
	public function setEntityManager(&$value = NULL){
		if ($value !== NULL && is_a($value, '\Doctrine\ORM\EntityManager'))
			$this->entityManager = $value;
		else
			$this->entityManager=\Yii::app()->doctrine->getEntityManager();
	}
	public function &getEntityManager(){
		if ($this->entityManager === NULL)
			$this->setEntityManager();
		
		return $this->entityManager;
	}
	
	/**
	 *@scField(name="id",DoctrineField="id",type="integer",primaryKey="true",hidden="true") 
	 */
	public function getid(){return $this->id;}
	public function setid($v){$this->id=$v;}
	
 	public function getVersion(){return $this->version;}
	
	public function getCreatorUserID(){return $this->creatorUserId;}
	public function setCreatorUserID($uid){$this->creatorUserId=$uid;}
	
	public function getModifierUserID(){return $this->modifierUserId;}
	public function setModifierUserID($uid){$this->modifierUserId=$uid;}
	
	public function getCreateDate(){return $this->dateCreated;}
	public function setCreateDate($d){$this->dateCreated=$d;}
	
	public function getdateLastModified(){return $this->dateLastModified;}
	public function setdateLastModified($d){$this->dateLastModified=$d;}
	
	public function getIsDeleted(){return $this->IsDeleted;}
	public function setIsDeleted($d){$this->IsDeleted=$d;}

	public function CreateClassFromScUsingMethod($functionName,$ExceptedProperties=NULL,$ValueArray = NULL){
		$reader = new AnnotationReader();
		$methods=get_class_methods(get_class($this));

		foreach ($methods as $methodName)
		{
			//Check That Method is getter or setter else continue
			if(!(substr($methodName, 0,3)=='get' ||	substr($methodName,0,3)=='set')	) continue;
			$propname = substr($methodName, 3,strlen($methodName)-3);
			//if propname is in $ExceptedProperties jump to next property
			if(is_array($ExceptedProperties))
				if(in_array($propname, $ExceptedProperties)) continue; 
			//Get Method Annotation
			$reflMethod = new \ReflectionMethod(get_class($this), $methodName);
			$MethodAnns = $reader->getMethodAnnotations($reflMethod);
			foreach ($MethodAnns as $annots){
				if(is_a($annots,'\IRERP\Basics\Annotations\scField')){
					//if defined Annotation is scField
					//Get Value From User
					$fieldvalue = call_user_func($functionName,$annots->name,$ValueArray);
					//Try To Set To Class 
					call_user_func(array(&$this, 'set'.$propname),$fieldvalue);
				}
			}
		}
	}
	
	public function GetByID($id)
	{
		return $this->getEntityManager()
					->getRepository(get_class($this))
					->find($id);
	}
	
	protected function parseObjectToArray()
	{
		return Common::parseObjectToArray($this);
	}

	protected function persistEntity()
	{
		$this->getEntityManager()->persist($this);
	}
	public function Save()
	{
		$this->persistEntity();
	}

	public function GetClassSCPropertiesInArray($ExceptedProperties=NULL){
		$rtnval=array();
		$isarray=is_array($ExceptedProperties);
		$reader=new AnnotationReader();
		$methods=get_class_methods(get_class($this));
		foreach ($methods as $methodName)
		{
			//Check That Method is getter or setter else continue
			if(!(substr($methodName, 0,3)=='get')) continue;
			$propname = substr($methodName, 3,strlen($methodName)-3);
			//if propname is in $ExceptedProperties jump to next property
			if($isarray) if(in_array($propname, $ExceptedProperties)) continue; 
			//Get Method Annotation
			$reflMethod= NULL;
			$reflMethod = new \ReflectionMethod(get_class($this), $methodName);
			$MethodAnns = $reader->getMethodAnnotations($reflMethod);
			foreach ($MethodAnns as $annots){
				if(is_a($annots,'\IRERP\Basics\Annotations\scField')){
					//if defined Annotation is scField
					//Get Value
					$rtnval[$annots->name]=	call_user_func(array(&$this, 'get'.$propname));
				}
			}
		}
		return $rtnval;
	}

	function GetClassPropertiesinArray($ExceptedProperties)
	{
		$rtnval = array();
		$isarray=is_array($ExceptedProperties);
		$methods = get_class_methods($this);
		print_r($methods);
		foreach($methods as $m)
		{
			if(substr($m,0,3)=='get'){
				$propname=substr($m,3);
				
				if($isarray){
					//Check That propname exist in ExcepredProperties
					if(in_array($propname,$ExceptedProperties)) continue;
				}
				
				$rtnval[$propname]=call_user_method($m,$this);
			}
		}
			
		return $rtnval;
	}


	function GetClassArray()
	{
		return $this->parseObjectToArray();
	}

	public function __construct()
	{
		$this->dateLastModified=new \DateTime();
		$this->dateCreated=new \DateTime();
		$this->EntityManager = Yii::app()->doctrine->getEntityManager();
	}

	public function findAll($parameters)
	{
		$tableAlias = 'tmp';
		
		$queryBuilder = $this->EntityManager->createQueryBuilder();
		$queryBuilder->add('select', $tableAlias)
					 ->add('from', get_class($this));
		
		if(isset($parameters['startRow']))
		{
			$queryBuilder->setFirstResult($parameters['startRow']);
			if(isset($parameters['endRow']))
				$queryBuilder->setMaxResults($parameters['endRow'] - $parameters['startRow']);
		}
		
		if(isset($parameters['orderBy']))
			foreach ($parameters['orderBy'] as $key => $value)
				$queryBuilder->addOrderBy($tableAlias.'.'.$key,$value);
		
		
	}
	
	public function fetchObjects($startRow,$endRow,$whstr,$whparam,$orderby){
		$em = $this->getEntityManager();
		
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'tmp')
		   ->add('from', get_class($this).' tmp')
		   ->setFirstResult( $startRow )
		   ->setMaxResults( $endRow-$startRow );

		$tmp=0;

		foreach($orderby as $fn=>$kn)
			if($tmp==0){
				$qb->orderBy('tmp.'.$fn,$kn);
				$tmp=1;
			}
			else
				$qb->addOrderBy('tmp.'.$fn,$kn);
		
		if($whstr!='') {
			$qb->add('where',$whstr.' and tmp.IsDeleted = 0 ');
			$qb->setParameters($whparam);
		}
		else
			$qb->add('where','tmp.IsDeleted =0');
		
		$query = $qb->getQuery();

//		var_dump($qb->getQuery()->getSQL());
//		var_dump($whparam);
//		var_dump($whstr); die;
		
		$results = $query->getResult();
			
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'count(tmp.id)')
		   ->add('from', get_class($this).' tmp');

		if($whstr!='') {
			$qb->add('where',$whstr.' and tmp.IsDeleted = 0 ');
			$qb->setParameters($whparam);
		} else
			$qb->add('where','tmp.IsDeleted =0');
			
		//get Total Rows
		$dql = $qb->getQuery();
		$tmptest = $dql->getResult();
		$totalRows = $tmptest[0][1];
		
		return array('totalRows'=>$totalRows,'results'=>$results);
	}
}
/***
 * Old
 * /*
function GetClassSCPropertiesInArray($ExceptedProperties=NULL)
{
$rtnval = array();
	$isarray=is_array($ExceptedProperties);
	$methods = get_class_methods($this);
	foreach($methods as $m){
		
		if(substr($m,0,5)=='scget'){
			$propname=substr($m,5);
			
			if($isarray){
				//Check That propname exist in ExcepredProperties
				if(in_array($propname,$ExceptedProperties)) continue;
			}
			
			$rtnval[$propname]= call_user_func(array(&$this, $m));
		}
	}
		
return $rtnval;
	
}

/*public function CreateClassFromScUsingMethod($functionName,$ExceptedProperties=NULL)
	{
	$isarray=is_array($ExceptedProperties);
	$methods = get_class_methods($this);
	foreach($methods as $m){
		
		if(substr($m,0,5)=='scset'){
			$propname=substr($m,5);
			
			if($isarray){
				//Check That propname exist in ExcepredProperties
				if(in_array($propname,$ExceptedProperties)) continue;
			}
			$rtn = call_user_func($functionName,$propname);
			//call_user_method($m,$this,$rtn);
			call_user_func(array(&$this, $m),$rtn);
		}
		
	}
	}
	public static function parseObjectToArray(){
	/*$object = $this;
    $array = array();
    if (is_object($object)) {
        $array = get_object_vars($object);
    }
    return $array;
}
*/
?>