<?php
! defined( 'ABSPATH' ) && exit();
 /**
 * @var \TotalRatingVendors\League\Plates\Template\Template $this
 * @var \TotalRating\Models\Widget $widget
 * @var \TotalRating\Entities\Entity $entity
 * @var \TotalRating\Template $template
 * @var string $apiBase
 */

?>
<template data-widget-uid="<?php echo esc_attr($widget->uid); ?>" data-entity-uid="<?php echo esc_attr($entity->getUid()); ?>">
    <!-- Style -->
    <?php foreach ($assets['css'] as $css): ?>
        <widget-link rel="stylesheet" href="<?php echo esc_attr($css); ?>" type="text/css"></widget-link>
    <?php endforeach; ?>
    <widget-style hidden>
        .widget {
        display: none;
        --color-primary: <?php echo esc_html($widget->getAttribute('settings.design.colors.primary.base')); ?>;
        --color-primary-contrast: <?php echo esc_html($widget->getAttribute('settings.design.colors.primary.contrast')); ?>;
        --color-secondary: <?php echo esc_html($widget->getAttribute('settings.design.colors.secondary.base')); ?>;
        --color-secondary-contrast: <?php echo esc_html($widget->getAttribute('settings.design.colors.secondary.contrast')); ?>;
        --color-background: <?php echo esc_html($widget->getAttribute('settings.design.colors.background.base')); ?>;
        --color-background-contrast: <?php echo esc_html($widget->getAttribute('settings.design.colors.background.contrast')); ?>;
        --color-dark: <?php echo esc_html($widget->getAttribute('settings.design.colors.dark.base')); ?>;
        --color-dark-contrast: <?php echo esc_html($widget->getAttribute('settings.design.colors.dark.contrast')); ?>;

        --scheme: var(<?php echo esc_html(sprintf('--scheme-%s', $widget->getAttribute('settings.design.scheme'))); ?>);
        --size: var(<?php echo esc_html(sprintf('--size-%s', $widget->getAttribute('settings.design.size'))); ?>);
        --space: var(<?php echo esc_html(sprintf('--space-%s', $widget->getAttribute('settings.design.space'))); ?>);
        --radius: var(<?php echo esc_html(sprintf('--radius-%s', $widget->getAttribute('settings.design.radius'))); ?>);
        }
    </widget-style>
    <widget-style hidden><?php echo esc_html($customCss); ?></widget-style>


    <!-- Wrapper -->
    <widget inline-template
            v-bind:widget="<?php echo esc_attr(json_encode($widget)); ?>"
            v-bind:entity="<?php echo esc_attr(json_encode($entity)); ?>"
            v-bind:options="<?php echo esc_attr(json_encode($options)); ?>"
            nonce="<?php echo esc_attr($nonce); ?>"
            api-base="<?php echo esc_attr($apiBase); ?>">

        <!-- Widget -->
        <div class="widget"
             v-bind:class="[widget ? 'is-ready':'' ,'position-'+(widget.settings.design.position || 'above')]">
            <h4 v-if="widget.title" class="title">{{widget.title}}</h4>
            <p v-if="widget.description" class="description">{{widget.description}}</p>

            <!-- Attributes -->
            <div class="attributes">
                <attribute inline-template
                           v-for="(attribute, attributeIndex) in attributes"
                           v-bind:widget="widget"
                           v-bind:attribute="attribute"
                           v-bind:attribute-index="attributeIndex"
                           v-bind:key="attributeIndex"
                           v-on:submit="onSubmit"
                           v-on:change="onChange"
                           v-on:revoke="onRevoke">

                    <div class="attribute" v-bind:class="classes">

                        <h4 v-if="attribute.label" class="label">{{attribute.label}}</h4>

                        <div class="error" v-if="attribute.error" v-text="attribute.error"></div>

                        <!-- Points -->
                        <div class="points">
                            <point inline-template
                                   v-for="(point, pointIndex) in attribute.points"
                                   v-bind:attribute="attribute"
                                   v-bind:attribute-index="attributeIndex"
                                   v-bind:point="point"
                                   v-bind:point-index="pointIndex"
                                   v-bind:has-rating="hasRating"
                                   v-bind:has-score="hasScore"
                                   v-bind:is-changing="isChanging"
                                   v-bind:display-score="shouldDisplayScore"
                                   v-bind:key="pointIndex">

                                <label class="point"
                                       v-bind:for="inputId"
                                       v-bind:tabindex="labelIndex"
                                       v-bind:class="labelClasses"
                                       v-on:keyup.enter="onSelect()"
                                       v-on:focus="onFocus()"
                                       v-on:blur="onBlur()">

                                    <!-- Symbol -->
                                    <div class="symbol">
                                        <point-symbol v-bind:symbol="point.symbol"></point-symbol>
                                    </div>

                                    <!-- Label -->
                                    <div class="label" v-if="point.label">{{point.label}}</div>

                                    <!-- Input -->
                                    <input type="radio"
                                           v-bind:id="inputId"
                                           v-bind:name="attribute.uid"
                                           v-bind:checked="isSelected"
                                           v-if="(!this.hasScore && !this.hasRating) || this.isChanging"
                                           v-on:change="onSelect()">

                                    <!-- Score -->
                                    <span class="score" v-if="shouldDisplayScore">{{ point.total || 0 }}</span>
                                </label>
                            </point>
                        </div>

                        <!-- Form -->
                        <rating-form inline-template
                                     v-if="canRate && hasSelection"
                                     v-bind:attribute="attribute"
                                     v-bind:widget="widget"
                                     v-on:submit="onSubmit">
                            <form class="form" v-on:submit.prevent="submit" v-if="shouldDisplayForm">
                                <div class="comment" v-if="shouldDisplayCommentField">
                                    <textarea rows="4" v-bind:placeholder="attribute.comment.message || '<?php echo esc_js(esc_html__('Your comment...', 'totalrating')); ?>'"
                                              v-model="attribute.comment.content"></textarea>
                                </div>

                                <button class="button" v-if="shouldDisplaySubmitButton" v-bind:disabled="attribute.comment.enabled && !attribute.comment.content?.trim()">
                                    <?php esc_html_e('Submit', 'totalrating'); ?>
                                </button>
                            </form>
                        </rating-form>

                        <!-- Score -->
                        <div class="score" v-html="score" v-if="isScale && shouldDisplayScore"></div>

                        <!-- Actions -->
                        <div v-if="canChange || canRevoke" class="actions">
                            <?php $or      = esc_html__('or', 'totalrating');
                            $actions = [];

                            if ($widget->isChangeAllowed()) {
                                $actions[] = sprintf(
                                    '<a href="#" v-on:click.prevent="onChangeRequest()">%s</a>',
                                    esc_html__('change', 'totalrating')
                                );
                            }

                            if ($widget->isRevokeAllowed()) {
                                $actions[] = sprintf(
                                    '<a href="#" v-on:click.prevent="onRevoke()">%s</a>',
                                    esc_html__('revoke', 'totalrating')
                                );
                            }

                            if (!empty($actions)) {
                                printf(
                                    esc_html__('You can %s your rating.', 'totalrating'),
                                    implode(" {$or} ", $actions)
                                );
                            }
                            ?>
                        </div>
                        <div v-else-if="isChanging" class="actions">
                            <a href="#"
                               v-on:click.prevent="onCancelChange">
                                <?php esc_html_e('Cancel', 'totalrating'); ?>
                            </a>
                        </div>
                    </div>
                </attribute>
            </div>
        </div>
    </widget>
</template>
