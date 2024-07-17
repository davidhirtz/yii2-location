<?php

namespace davidhirtz\yii2\location\models;

use davidhirtz\yii2\datetime\DateTime;
use davidhirtz\yii2\datetime\DateTimeBehavior;
use davidhirtz\yii2\location\modules\ModuleTrait;
use davidhirtz\yii2\location\validators\CoordinateValidator;
use davidhirtz\yii2\skeleton\behaviors\BlameableBehavior;
use davidhirtz\yii2\skeleton\behaviors\TimestampBehavior;
use davidhirtz\yii2\skeleton\behaviors\TrailBehavior;
use davidhirtz\yii2\skeleton\db\ActiveRecord;
use davidhirtz\yii2\skeleton\models\interfaces\DraftStatusAttributeInterface;
use davidhirtz\yii2\skeleton\models\interfaces\TypeAttributeInterface;
use davidhirtz\yii2\skeleton\models\traits\DraftStatusAttributeTrait;
use davidhirtz\yii2\skeleton\models\traits\I18nAttributesTrait;
use davidhirtz\yii2\skeleton\models\traits\TypeAttributeTrait;
use davidhirtz\yii2\skeleton\models\traits\UpdatedByUserTrait;
use davidhirtz\yii2\skeleton\validators\DynamicRangeValidator;
use Yii;

/**
 * @property int $id
 * @property int $status
 * @property int $type
 * @property string $name
 * @property string|null $formatted_address
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $street
 * @property string|null $house_number
 * @property string|null $locality
 * @property string|null $postal_code
 * @property string|null $district
 * @property string|null $state
 * @property string|null $country_code
 * @property string|null $provider_id
 * @property DateTime|null $updated_at
 * @property DateTime $created_at
 */
class Location extends ActiveRecord implements DraftStatusAttributeInterface, TypeAttributeInterface
{
    use DraftStatusAttributeTrait;
    use I18nAttributesTrait;
    use ModuleTrait;
    use TypeAttributeTrait;
    use UpdatedByUserTrait;

    public const AUTH_LOCATION_CREATE = 'locationCreate';
    public const AUTH_LOCATION_DELETE = 'locationDelete';
    public const AUTH_LOCATION_UPDATE = 'locationUpdate';

    public function behaviors(): array
    {
        return [
            ...parent::behaviors(),
            'DateTimeBehavior' => DateTimeBehavior::class,
            'TrailBehavior' => TrailBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [
                ['status', 'type', 'country_code'],
                DynamicRangeValidator::class,
            ],
            [
                ['name', 'formatted_address', 'street', 'house_number', 'locality', 'postal_code', 'district', 'state'],
                'string',
                'max' => 255,
            ],
            [
                ['provider_id'],
                'string',
            ],
            [
                ['lat', 'lng'],
                CoordinateValidator::class,
            ],
        ];
    }

    public function beforeSave($insert): bool
    {
        $this->attachBehaviors([
            'BlameableBehavior' => BlameableBehavior::class,
            'TimestampBehavior' => TimestampBehavior::class,
        ]);

        return parent::beforeSave($insert);
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailAttributes(): array
    {
        return array_diff($this->attributes(), [
            'updated_by_user_id',
            'updated_at',
            'created_at',
        ]);
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelName(): string
    {
        if ($this->id) {
            return $this->getI18nAttribute('name') ?: Yii::t('skeleton', '{model} #{id}', [
                'model' => $this->getTrailModelType(),
                'id' => $this->id,
            ]);
        }

        return $this->getTrailModelType();
    }

    public function getTrailModelType(): string
    {
        return Yii::t('location', 'Location');
    }

    public function getAdminRoute(): array
    {
        return ['/admin/location/update', 'id' => $this->id];
    }

    /**
     * @noinspection PhpUnused
     */
    public function getTrailModelAdminRoute(): array|false
    {
        return $this->id ? $this->getAdminRoute() : false;
    }

    public static function getCountryCodes(): array
    {
        return require(Yii::getAlias('@skeleton/messages/') . Yii::$app->language . '/countries.php');
    }

    public function attributeLabels(): array
    {
        return [
            ...parent::attributeLabels(),
            'name' => Yii::t('location', 'Name'),
            'formatted_address' => Yii::t('location', 'Formatted address'),
            'street' => Yii::t('location', 'Street'),
            'house_number' => Yii::t('location', 'House number'),
            'locality' => Yii::t('location', 'City'),
            'postal_code' => Yii::t('location', 'Postal code'),
            'district' => Yii::t('location', 'District'),
            'state' => Yii::t('location', 'State'),
            'country_code' => Yii::t('location', 'Country'),
            'lat' => Yii::t('location', 'Latitude'),
            'lng' => Yii::t('location', 'Longitude'),
            'provider_id' => Yii::t('location', 'Provider ID'),
        ];
    }

    public function formName(): string
    {
        return 'Location';
    }

    public static function tableName(): string
    {
        return static::getModule()->getTableName('location');
    }
}
