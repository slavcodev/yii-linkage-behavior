<?php
/**
 * Linkage class file.
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * Class Linkage
 *
 * @property CActiveRecord $owner
 * @method CActiveRecord getOwner()
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 * @package yiiext/linkage-behavior
 */
class Linkage extends CBehavior
{
	const ERROR_PK_EMPTY = 'Unable to link models: the primary key of %s is null.';
	const ERROR_SAVE_ASSN = 'Unable to link models: cannot save association.';
	const ERROR_SAVE = 'Unable to save %s model.';
	const ERROR_DELETE = 'Unable to delete %s model.';
	const ERROR_UNSUPPORT_RELATION = 'Unsupport relation type.';

	/**
	 * Establishes the relationship between two models.
	 *
	 * Note that this method requires that the primary key value is not null.
	 *
	 * @param string $name the name of the relationship
	 * @param ActiveRecord $model the model to be linked with the current one.
	 * @param bool $save
	 * @param array $extraColumns additional column values to be saved into the pivot table.
	 * This parameter is only meaningful for a relationship involving a pivot table
	 * @return ActiveRecord
	 * @throws CException if the method is unable to link two models.
	 */
	public function link($name, $model, $save = true, array $extraColumns = array())
	{
		$owner = $this->getOwner();
		$relation = $this->getActiveRelation($name);

		if (!$owner->getPrimaryKey()) {
			throw new CException(sprintf(self::ERROR_PK_EMPTY, get_class($owner)));
		}

		if ($relation instanceof CManyManyRelation) {
			if ($save && !$model->save(false)) {
				throw new CException(sprintf(self::ERROR_SAVE, get_class($model)));
			}

			if (!$model->getPrimaryKey()) {
				throw new CException(sprintf(self::ERROR_PK_EMPTY, get_class($model)));
			}

			list ($pk, $fk) = $relation->getJunctionForeignKeys();
			$extraColumns[$pk] = $owner->primaryKey;
			$extraColumns[$fk] = $model->primaryKey;

			$pivotTable = $relation->getJunctionTableName();
			$command = $owner->getCommandBuilder()->createInsertCommand($pivotTable, $extraColumns);
			if(!$command->execute()) {
				throw new CException(sprintf(self::ERROR_SAVE_ASSN, $pivotTable));
			}
		} elseif ($relation->through !== null) {
			if ($save && !$model->save(false)) {
				throw new CException(sprintf(self::ERROR_SAVE, get_class($model)));
			}

			if (!$model->getPrimaryKey()) {
				throw new CException(sprintf(self::ERROR_PK_EMPTY, get_class($model)));
			}

			$bridgeRelation = $this->getActiveRelation($relation->through);
			/** @var CActiveRecord $bridgeModel */
			$bridgeModel = new $bridgeRelation->className;

			$extraColumns[$bridgeRelation->foreignKey] = $owner->primaryKey;
			$extraColumns[$relation->foreignKey] = $model->primaryKey;

			foreach ($extraColumns as $attribute => $value) {
				$bridgeModel->$attribute = $value;
			}

			if (!$bridgeModel->save(false)) {
				throw new CException(sprintf(self::ERROR_SAVE_ASSN, $bridgeModel->tableName()));
			}
		} else {
			$fk = $relation->foreignKey;
			$model->$fk = $owner->getPrimaryKey();

			if ($save && !$model->save(false)) {
				throw new CException(sprintf(self::ERROR_SAVE, get_class($model)));
			}
		}

		// Update lazily loaded related objects
		if (!$relation instanceof CHasManyRelation) {
			$owner->addRelatedRecord($name, $model, false);
		} else {
			$index = $relation->index;
			$owner->addRelatedRecord($name, $model, $index ? $model->$index : true);
		}

		return $owner;
	}

	/**
	 * @param string $name the name of the relationship
	 * @param ActiveRecord $model the model to be unlinked with the current one
	 * @param boolean $delete whether to delete the model or unset foreign key.
	 * @return CActiveRecord
	 * @throws CException if the method is unable to unlink two models.
	 */
	public function unlink($name, $model, $delete = false)
	{
		$owner = $this->getOwner();
		$relation = $this->getActiveRelation($name);

		if ($relation instanceof CManyManyRelation) {
			list ($pk, $fk) = $relation->getJunctionForeignKeys();
			$columns = array();
			$columns[$pk] = $owner->primaryKey;
			$columns[$fk] = $model->primaryKey;

			$pivotTable = $relation->getJunctionTableName();
			$criteria = new CDbCriteria();
			$criteria->addColumnCondition($columns);
			$command = $owner->getCommandBuilder()->createDeleteCommand($pivotTable, $criteria);
			if(!$command->execute()) {
				throw new CException(sprintf(self::ERROR_SAVE_ASSN, $pivotTable));
			}
		} elseif ($relation->through !== null) {
			$bridgeRelation = $this->getActiveRelation($relation->through);
			$columns = array();
			$columns[$bridgeRelation->foreignKey] = $owner->primaryKey;
			$columns[$relation->foreignKey] = $model->primaryKey;

			/** @var CActiveRecord $bridgeModel */
			$bridgeModel = CActiveRecord::model($bridgeRelation->className)
				->findByAttributes($columns);

			if ($delete && !$bridgeModel->delete()) {
				throw new CException(sprintf(self::ERROR_DELETE, get_class($bridgeModel)));
			} else {
				foreach ($columns as $key => $value) {
					$bridgeModel->$key = null;
				}
				if (!$bridgeModel->save(false)) {
					throw new CException(sprintf(self::ERROR_SAVE, get_class($bridgeModel)));
				}
			}
		} else {
			if (!$delete) {
				foreach ($this->getRelationKeys($relation) as $fk => $pk) {
					$model->$fk = null;
				}
				if (!$model->save(false)) {
					throw new CException(sprintf(self::ERROR_SAVE, get_class($model)));
				}
			} elseif ($model->getIsNewRecord() || !$model->delete()) {
				throw new CException(sprintf(self::ERROR_DELETE, get_class($model)));
			}
		}

		// Update lazily loaded related objects
		if (!$relation instanceof CHasManyRelation) {
			unset($owner->$name);
		} elseif (($index = $relation->index) !== null) {
			unset($owner->$name[$index]);
		} else {
			$models = array();
			foreach ($owner->getRelated($name) as $related) {
				if (!$model->equals($related)) {
					array_push($models, $related);
				}
			}
			unset($owner->$name);
			$owner->addRelatedRecord($name, $models, false);
		}

		return $owner;
	}

	/**
	 * @param string $name
	 * @return CActiveRelation|CHasOneRelation|CHasManyRelation|CManyManyRelation
	 * @throws CException if the method is unable to link two models.
	 */
	private function getActiveRelation($name)
	{
		$relation = $this->getOwner()->getActiveRelation($name);
		if ($relation instanceof CBelongsToRelation) {
			throw new CException(sprintf(self::ERROR_UNSUPPORT_RELATION, get_class($relation)));
		}
		return $relation;
	}

	private function getRelationKeys($relation)
	{
		return array(
			$relation->foreignKey => $this->getOwner()->getPrimaryKey()
		);
	}
}