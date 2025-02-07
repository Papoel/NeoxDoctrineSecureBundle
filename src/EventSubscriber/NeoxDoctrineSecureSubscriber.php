<?php
    
    namespace NeoxDoctrineSecure\NeoxDoctrineSecureBundle\EventSubscriber;
    
    use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
    use Doctrine\ORM\Event\PostFlushEventArgs;
    use Doctrine\ORM\Event\PostLoadEventArgs;
    use Doctrine\ORM\Event\OnFlushEventArgs;
    use Doctrine\ORM\Events;
    use NeoxDoctrineSecure\NeoxDoctrineSecureBundle\Pattern\NeoxDoctrineSecure;
    use ReflectionException;
    
    /**
     * Doctrine event subscriber which encrypt/decrypt entities
     */
    
    #[AsDoctrineListener(event: Events::postLoad, priority: 10, connection: 'default')]
    #[AsDoctrineListener(event: Events::onFlush, priority: 500, connection: 'default')]
    #[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
    class NeoxDoctrineSecureSubscriber
    {
        public function __construct(readonly NeoxDoctrineSecure $neoxCryptorService) {}
        
        /**
         * Listen a postLoad lifecycle event.
         * Decrypt entities property's values when loaded into the entity manger
         *
         * @param PostLoadEventArgs $args
         *
         * @throws ReflectionException
         */
        public function postLoad(PostLoadEventArgs $args): void
        {
            $entity = $args->getObject();
            $this->neoxCryptorService->decryptFields($entity);
        }
        
        /**
         * Listen to postFlush event
         * Decrypt entities after having been inserted into the database
         *
         * @param PostFlushEventArgs $postFlushEventArgs
         *
         * @throws ReflectionException
         */
        public function postFlush(PostFlushEventArgs $postFlushEventArgs): void
        {
            foreach ($postFlushEventArgs->getObjectManager()->getUnitOfWork()->getIdentityMap() as $entityMap) {
                foreach ($entityMap as $entity) {
                    $this->neoxCryptorService->decryptFields($entity);
                }
            }
        }
        
        /**
         * Listen to onflush event
         * Encrypt entities that are inserted into the database
         *
         * @param OnFlushEventArgs $onFlushEventArgs
         *
         * @throws ReflectionException
         */
        public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
        {
            $unitOfWork = $onFlushEventArgs->getObjectManager()->getUnitOfWork();
            
            foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
                $this->newItem($entity, $onFlushEventArgs, $unitOfWork);
            }
            foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
                $this->updateItem($unitOfWork);
            }
        }
        
        public static function getSubscribedEvents(): array
        {
            return [
                Events::postLoad,
                Events::onFlush,
                Events::postFlush,
            ];
        }
        
        /**
         * @param mixed            $entity
         * @param OnFlushEventArgs $onFlushEventArgs
         * @param                  $unitOfWork
         *
         * @return void
         * @throws ReflectionException
         */
        private function newItem(mixed $entity, OnFlushEventArgs $onFlushEventArgs, $unitOfWork): void
        {
            $encryptCounterBefore = $this->neoxCryptorService->counterSecure;
            $this->neoxCryptorService->encryptFields($entity);
            if ($this->neoxCryptorService->counterSecure > $encryptCounterBefore) {
                $classMetadata = $onFlushEventArgs->getObjectManager()->getClassMetadata(get_class($entity));
                $unitOfWork->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
        
        /**
         * @param $unitOfWork
         *
         * @return void
         * @throws ReflectionException
         */
        
        private function updateItem($unitOfWork): void
        {
            foreach ($unitOfWork->getIdentityMap() as $entityName => $entityArray) {
                if (isset($this->neoxCryptorService->cachedEntity[$entityName])) {
                    foreach ($entityArray as $entityId => $instance) {
                        $this->neoxCryptorService->encryptFields($instance);
                    }
                }
            }
            $this->neoxCryptorService->cachedEntity = [];
        }
    }