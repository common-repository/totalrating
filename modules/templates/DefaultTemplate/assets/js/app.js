"use strict";
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (g && (g = 0, op[0] && (_ = 0)), _) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var WidgetComponent = Vue.component('widget', {
    data: function () {
        return {
            attributes: this.widget.attributes.map(this.prepareAttribute.bind(this)),
        };
    },
    props: {
        widget: {
            type: Object,
            default: function () {
                return {};
            }
        },
        entity: {
            type: Object,
            default: function () {
                return {};
            }
        },
        options: {
            type: Object,
            default: function () {
                return {};
            }
        },
        nonce: {
            type: String,
            default: null
        },
        apiBase: {
            type: String,
            default: '/wp-json/totalrating'
        },
    },
    methods: {
        obtainRecaptchaToken: function () {
            return __awaiter(this, void 0, void 0, function () {
                var _a;
                return __generator(this, function (_b) {
                    switch (_b.label) {
                        case 0:
                            if (!this.options.recaptcha.enabled) return [3, 2];
                            return [4, grecaptcha.execute("".concat(this.options.recaptcha.key), { action: 'rate' })];
                        case 1:
                            _a = _b.sent();
                            return [3, 3];
                        case 2:
                            _a = new Promise(function (resolve) { return resolve(''); });
                            _b.label = 3;
                        case 3: return [2, _a];
                    }
                });
            });
        },
        prepareAttribute: function (attribute) {
            attribute.focused = false;
            attribute.processing = false;
            attribute.changing = false;
            attribute.error = null;
            attribute.selected = attribute.points.find(function (point) { return point.uid === attribute.checked; }) || null;
            return attribute;
        },
        onChange: function (data) {
            return __awaiter(this, void 0, void 0, function () {
                return __generator(this, function (_a) {
                    this.onSubmit(data);
                    return [2];
                });
            });
        },
        onRevoke: function (_a) {
            return __awaiter(this, arguments, void 0, function (_b) {
                var request, _c, _d, response, e_1;
                var _e, _f;
                var _g;
                var attribute = _b.attribute;
                return __generator(this, function (_h) {
                    switch (_h.label) {
                        case 0:
                            attribute.processing = true;
                            attribute.error = null;
                            if ((_g = this.widget) === null || _g === void 0 ? void 0 : _g.preview) {
                                attribute.processing = false;
                                attribute.selected.total = 0;
                                attribute.selected = null;
                                attribute.checked = null;
                                attribute.rating = null;
                                attribute.statistics.visible = false;
                                attribute.statistics.total = 0;
                                this.$set(this.attributes, this.attributes.indexOf(attribute), this.prepareAttribute(__assign({}, attribute)));
                                return [2, true];
                            }
                            _d = (_c = jQuery).ajax;
                            _e = {
                                type: 'DELETE',
                                url: "".concat(this.apiBase, "/rating"),
                                headers: this.nonce ? { 'X-WP-Nonce': this.nonce } : {}
                            };
                            _f = {
                                rating_uid: attribute.rating
                            };
                            return [4, this.obtainRecaptchaToken()];
                        case 1:
                            request = _d.apply(_c, [(_e.data = (_f.recaptcha = _h.sent(),
                                    _f),
                                    _e.dataType = 'json',
                                    _e)]);
                            _h.label = 2;
                        case 2:
                            _h.trys.push([2, 4, , 5]);
                            return [4, request];
                        case 3:
                            response = _h.sent();
                            if (response.success) {
                                this.$set(this.attributes, this.attributes.indexOf(attribute), this.prepareAttribute(response.data.attribute));
                                this.$root.$emit('revoked', { attribute: attribute });
                            }
                            else {
                                throw new Error(response.error);
                            }
                            return [3, 5];
                        case 4:
                            e_1 = _h.sent();
                            alert("Something went wrong: ".concat(e_1.statusText, " (").concat(e_1.responseText || e_1.message, ")"));
                            return [3, 5];
                        case 5:
                            attribute.processing = false;
                            return [2];
                    }
                });
            });
        },
        onSubmit: function (_a) {
            return __awaiter(this, arguments, void 0, function (_b) {
                var request, _c, _d, response, e_2;
                var _e, _f;
                var _g, _h;
                var attribute = _b.attribute;
                return __generator(this, function (_j) {
                    switch (_j.label) {
                        case 0:
                            attribute.processing = true;
                            attribute.error = null;
                            if ((_g = this.widget) === null || _g === void 0 ? void 0 : _g.preview) {
                                attribute.checked = attribute.selected.uid;
                                attribute.rating = 'test';
                                attribute.statistics.visible = true;
                                attribute.statistics.total = 1;
                                if (!attribute.statistics.template) {
                                    attribute.statistics.template = attribute.statistics.text;
                                }
                                attribute.selected.total = 1;
                                attribute.statistics.text = attribute.statistics.template.replace('0', attribute.selected.label);
                                this.$set(this.attributes, this.attributes.indexOf(attribute), this.prepareAttribute(__assign({}, attribute)));
                                this.$root.$emit('rated', { attribute: attribute });
                                return [2, true];
                            }
                            _d = (_c = jQuery).ajax;
                            _e = {
                                type: attribute.rating ? 'PATCH' : 'POST',
                                url: "".concat(this.apiBase, "/rating"),
                                headers: this.nonce ? { 'X-WP-Nonce': this.nonce } : {}
                            };
                            _f = {
                                widget_uid: this.widget.uid,
                                attribute_uid: attribute.uid,
                                rating_uid: attribute.rating,
                                point_uid: attribute.selected.uid,
                                comment: (_h = attribute.comment) === null || _h === void 0 ? void 0 : _h.content
                            };
                            return [4, this.obtainRecaptchaToken()];
                        case 1:
                            request = _d.apply(_c, [(_e.data = (_f.recaptcha = _j.sent(),
                                    _f.entity_id = this.entity.id,
                                    _f.entity_type = this.entity.type,
                                    _f.entity_meta = this.entity.meta,
                                    _f),
                                    _e.dataType = 'json',
                                    _e)]);
                            _j.label = 2;
                        case 2:
                            _j.trys.push([2, 4, , 5]);
                            return [4, request];
                        case 3:
                            response = _j.sent();
                            if (response.success) {
                                attribute.rating = response.data.uid;
                                this.$set(this.attributes, this.attributes.indexOf(attribute), this.prepareAttribute(response.data.attribute));
                                this.$root.$emit('rated', { attribute: attribute });
                                if (response.data.redirect) {
                                    window.location = response.data.redirect;
                                }
                            }
                            else {
                                throw new Error(response.error);
                            }
                            return [3, 5];
                        case 4:
                            e_2 = _j.sent();
                            attribute.error = "".concat(e_2.responseJSON ? e_2.responseJSON.error : e_2.error);
                            return [3, 5];
                        case 5:
                            attribute.processing = false;
                            return [2];
                    }
                });
            });
        },
    }
});
var AttributeComponent = Vue.component('attribute', {
    props: {
        widget: {
            type: Object,
            default: function () {
                return {};
            }
        },
        attribute: {
            type: Object,
            default: function () {
                return {};
            }
        },
        attributeIndex: {
            type: Number,
            default: 0
        }
    },
    computed: {
        classes: function () {
            var classes = ["is-".concat(this.attribute.type)];
            this.attribute.selected && classes.push('has-checked');
            this.attribute.focused && classes.push('has-focus');
            this.attribute.processing && classes.push('is-processing');
            this.hasRating && classes.push('has-rated');
            this.hasScore && classes.push('has-score');
            this.canRate && classes.push('can-rate');
            return classes;
        },
        score: function () {
            var _a;
            return (_a = this.attribute.statistics) === null || _a === void 0 ? void 0 : _a.text;
        },
        canRate: function () {
            if (this.widget.skipToResults) {
                return false;
            }
            return (this.attribute.canRate && !this.hasRating) || this.attribute.changing;
        },
        canChange: function () {
            return this.hasRating && this.attribute.canChange;
        },
        canRevoke: function () {
            return this.hasRating && this.attribute.canRevoke;
        },
        hasSelection: function () {
            return this.attribute.selected;
        },
        hasScore: function () {
            return !this.canRate && this.shouldDisplayScore;
        },
        hasRating: function () {
            return Boolean(this.attribute.rating) && !this.isChanging && !this.widget.skipToResults;
        },
        isChanging: function () {
            return this.attribute.changing;
        },
        isScale: function () {
            return this.attribute.type === 'scale';
        },
        isCount: function () {
            return this.attribute.type === 'count';
        },
        shouldDisplayScore: function () {
            return this.attribute.statistics.visible && this.attribute.statistics.total > 0;
        }
    },
    methods: {
        onChangeRequest: function () {
            this.attribute.changing = true;
            this.attribute.lastChecked = this.attribute.checked;
            this.attribute.lastSelected = this.attribute.selected;
            this.attribute.checked = null;
            this.attribute.selected = null;
        },
        onCancelChange: function () {
            this.attribute.changing = false;
            this.attribute.checked = this.attribute.lastChecked;
            this.attribute.selected = this.attribute.lastSelected;
        },
        onSubmit: function () {
            this.attribute.changing = false;
            if (this.hasRating) {
                this.$emit('change', { attribute: this.attribute });
            }
            else {
                this.$emit('submit', { attribute: this.attribute });
            }
        },
        onRevoke: function () {
            this.$emit('revoke', { attribute: this.attribute });
        }
    }
});
var PointComponent = Vue.component('point', {
    template: '<slot></slot>',
    props: {
        attribute: {
            type: Object,
            default: function () {
                return {};
            }
        },
        attributeIndex: {
            type: Number,
            default: 0
        },
        point: {
            type: Object,
            default: function () {
                return {};
            }
        },
        pointIndex: {
            type: Number,
            default: 0
        },
        hasRating: {
            type: Boolean,
            default: false
        },
        isChanging: {
            type: Boolean,
            default: false
        },
        hasScore: {
            type: Boolean,
            default: false
        },
        canRate: {
            type: Boolean,
            default: true
        },
        displayScore: {
            type: Boolean,
            default: true
        },
    },
    computed: {
        inputId: function () {
            return 'point-' + this.point.uid;
        },
        labelIndex: function () {
            return (this.pointIndex + 1) + (this.attributeIndex * 10);
        },
        labelClasses: function () {
            var classes = [];
            if (this.point == this.attribute.selected) {
                classes.push('is-checked');
            }
            else if (!this.attribute.selected && this.pointIndex + 1 == Math.floor(this.attribute.statistics.avg)) {
                classes.push('is-checked');
            }
            else if (Math.floor(this.attribute.statistics.avg) == 0) {
                classes.push('is-unchecked');
            }
            this.point.focused && classes.push('has-focus');
            return classes;
        },
        isSelected: function () {
            return this.attribute.selected === this.point;
        },
        shouldDisplayScore: function () {
            return this.attribute.type === 'count' && this.displayScore;
        }
    },
    methods: {
        onFocus: function () {
            if (this.hasRating || this.hasScore) {
                return;
            }
            this.$set(this.attribute, 'focused', true);
            this.$set(this.point, 'focused', true);
        },
        onBlur: function () {
            if (this.hasRating) {
                return;
            }
            this.$set(this.attribute, 'focused', false);
            this.$set(this.point, 'focused', false);
        },
        setChecked: function () {
            if (this.hasRating) {
                return;
            }
            this.$set(this.attribute, 'checked', this.point.uid);
        },
        onSelect: function () {
            if ((!this.hasScore && !this.hasRating) || this.isChanging) {
                this.$set(this.attribute, 'selected', this.point);
            }
        },
    },
});
var RatingSymbol = Vue.component('point-symbol', {
    props: {
        symbol: {
            type: Object,
            default: function () {
                return {};
            }
        }
    },
    functional: true,
    render: function (createElement, context) {
        if (context.props.symbol.type === 'text') {
            return context._v(context.props.symbol.default);
        }
        else if (context.props.symbol.type === 'url') {
            return createElement('img', {
                attrs: {
                    src: context.props.symbol.default,
                    alt: context.props.symbol.label
                }
            });
        }
        return context.props.symbol.default;
    }
});
var RatingFormComponent = Vue.component('rating-form', {
    props: {
        widget: {
            type: Object,
            default: function () {
                return {};
            }
        },
        attribute: {
            type: Object,
            default: function () {
                return {};
            }
        }
    },
    computed: {
        commentThresholdPoints: function () {
            var _this = this;
            var _a;
            if ((_a = this.attribute.comment) === null || _a === void 0 ? void 0 : _a.thresholdPoint) {
                var thresholdPoint = this.attribute.points.find(function (point) { return point.uid === _this.attribute.comment.thresholdPoint; });
                var points = [];
                for (var _i = 0, _b = this.attribute.points; _i < _b.length; _i++) {
                    var point = _b[_i];
                    if (point.value >= thresholdPoint.value) {
                        points.push(point);
                    }
                }
                return points;
            }
            return this.attribute.points;
        },
        shouldDisplayForm: function () {
            return this.widget.settings.behaviours.confirmation.enabled || this.shouldDisplayCommentField;
        },
        shouldDisplayCommentField: function () {
            var _this = this;
            if (this.attribute.comment.thresholdPoint) {
                var thresholdPoint = this.attribute.points.filter(function (point) { return point.uid === _this.attribute.comment.thresholdPoint; });
            }
            return this.attribute.comment &&
                this.attribute.comment.enabled &&
                this.commentThresholdPoints.includes(this.attribute.selected) &&
                !this.attribute.checked;
        },
        shouldDisplaySubmitButton: function () {
            return this.shouldDisplayForm && this.attribute.selected && !this.attribute.checked;
        },
    },
    methods: {
        submit: function () {
            this.$emit('submit', { attribute: this.attribute });
        }
    },
    watch: {
        shouldDisplaySubmitButton: function (newValue, oldValue) {
            if (newValue === false) {
                this.submit();
            }
        }
    },
    mounted: function () {
        if (!this.shouldDisplaySubmitButton) {
            this.submit();
        }
    }
});
function TotalRating(wrapperElement) {
    if (wrapperElement.querySelector('.totalrating-widget')) {
        return;
    }
    var template = document.querySelector("template[data-widget-uid=\"".concat(wrapperElement.getAttribute('data-widget-uid'), "\"][data-entity-uid=\"").concat(wrapperElement.getAttribute('data-entity-uid'), "\"]"));
    var host = document.createElement('div');
    host.classList.add('totalrating-widget');
    host.attachShadow({ mode: 'open' }).append(template.cloneNode(true).content);
    wrapperElement.appendChild(host);
    host.shadowRoot.querySelectorAll("widget-style, widget-link, widget-script").forEach(function (el) {
        var style = document.createElement(el.tagName.toLowerCase().replace("widget-", ""));
        style.textContent = el.textContent;
        el.getAttributeNames().forEach(function (name) { return style.setAttribute(name, el.getAttribute(name)); });
        el.after(style);
        el.remove();
    });
    new Vue({
        el: host.shadowRoot.querySelector('widget'),
        mounted: function () {
            (host.shadowRoot).instance = this;
            wrapperElement.querySelector('.totalrating-loading').remove();
        }
    });
}
function initRatingWidgets() {
    document.querySelectorAll('.totalrating-widget-wrapper').forEach(function (wrapperElement) { return TotalRating(wrapperElement); });
}
initRatingWidgets();
if (window['lazyLoadRatingWidgets']) {
    setInterval(initRatingWidgets, 2000);
}
