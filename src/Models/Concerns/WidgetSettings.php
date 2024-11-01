<?php

namespace TotalRating\Models\Concerns;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Filters\FilterWidgetEntities;
use TotalRating\Models\Attribute;
use TotalRating\Models\WorkflowRule;
use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

trait WidgetSettings
{
    /**
     * @param  string  $key
     * @param  null  $default
     *
     * @return mixed
     */
    public function getSettings($key, $default = null)
    {
        return $this->getAttribute("settings.{$key}", $default);
    }

    /**
     * @return Collection<Attribute>
     */
    public function getRatingAttributes(): Collection
    {
        return $this->getAttribute('attributes', Collection::create());
    }

    public function allowOtherContext()
    {
        return in_array('widget', $this->getEntities(), true);
    }

    /**
     * @param $attributeUid
     *
     * @return Attribute|null
     */
    public function getRatingAttribute($attributeUid)
    {
        return $this->getRatingAttributes()->first(
            static function (Attribute $attribute) use ($attributeUid) {
                return $attribute->getUid() === $attributeUid;
            }
        );
    }

    /**
     * @return bool
     */
    public function isResultsVisible(): bool
    {
        return !$this->isResultsHidden();
    }

    /**
     * @return bool
     */
    public function isResultsHidden(): bool
    {
        return $this->getSettings('behaviours.hideResults.enabled', false);
    }

    /**
     * @return bool
     */
    public function isResultsHiddenForAll(): bool
    {
        return $this->isResultsHidden() && $this->getVisibilityScope() === 'all';
    }

    /**
     * @return bool
     */
    public function isResultsHiddenForNonVoters(): bool
    {
        return $this->isResultsHidden() && $this->getVisibilityScope() === 'non-voters';
    }

    /**
     * @return string
     */
    public function getVisibilityScope(): string
    {
        return $this->getSettings('behaviours.hideResults.for', 'all');
    }

    /**
     * @return Collection
     */
    public function getDesignSettings(): Collection
    {
        return Collection::create($this->getSettings('design', []));
    }

    /**
     * @return string
     */
    public function getEmplacement(): string
    {
        return $this->getSettings('design.emplacement', 'after_content');
    }

    /**
     * @return bool
     */
    public function isPlacedBeforeContent(): bool
    {
        return $this->getEmplacement() === 'before_content';
    }

    /**
     * @return bool
     */
    public function isPlacedAfterContent(): bool
    {
        return $this->getEmplacement() === 'after_content';
    }

    /**
     * @return array
     */
    public function getEntities(): array
    {
        $entities = $this->getSettings('rules.entities', []);

        return FilterWidgetEntities::apply($entities, $this);
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->getSettings('design.template', 'default-template');
    }

    /**
     * @return bool
     */
    public function isChangeAllowed(): bool
    {
        return $this->getSettings('behaviours.change.enabled', false);
    }

    /**
     * @return bool
     */
    public function isRevokeAllowed(): bool
    {
        return $this->getSettings('behaviours.revoke.enabled', false);
    }

    /**
     * @return bool
     */
    public function isAutoIntegrated(): bool
    {
        return $this->getSettings('behaviours.autoIntegrate.enabled', true);
    }

    /**
     * @return int
     */
    public function getLimitationTimeout(): int
    {
        return $this->getSettings('limitations.timeout', 60);
    }

    /**
     * @return int
     */
    public function getLimitationPerSession(): int
    {
        return $this->getSettings('limitations.session', 1);
    }

    /**
     * @return int
     */
    public function getLimitationPerEntity(): int
    {
        return $this->getSettings('limitations.entity', 1);
    }

    /**
     * @return bool
     */
    public function isLimitationByCookiesEnabled(): bool
    {
        return $this->getSettings('limitations.cookies.enabled', false);
    }

    /**
     * @return bool
     */
    public function isLimitationByIpEnabled(): bool
    {
        return $this->getSettings('limitations.ip.enabled', false);
    }

    /**
     * @return bool
     */
    public function isLimitationByUserEnabled(): bool
    {
        return $this->getSettings('limitations.user.enabled', false);
    }

    /**
     * @return Collection<WorkflowRule>|WorkflowRule[]
     */
    public function getWorkflowRules()
    {
        return Collection::create($this->getSettings('workflow.rules', []))->transform(
            function ($rule) {
                return new WorkflowRule($this, $rule);
            }
        );
    }

    public function withSkipToResults()
    {
        $this->setAttribute('skipToResults', true);

        return $this;
    }

    public function withMinimalSettings()
    {
        $this->setAttribute('settings.design.template', 'default-template');
        $this->setAttribute('settings.behaviours.hideResults.enabled', false);
        $this->setAttribute('description', '');
        $this->setAttribute('settings.design.colors.background.base', 'transparent');

        return $this;
    }
}
