! function(e) {
	"use strict";
	var t = function(t, i) {
		this.$element = e(t), this.$indicators = this.$element.find(".carousel-indicators"), this.options = i, "hover" == this.options.pause && this.$element.on("mouseenter", e.proxy(this.pause, this)).on("mouseleave", e.proxy(this.cycle, this))
	};
	t.prototype = {
		cycle: function(t) {
			return t || (this.paused = !1), this.interval && clearInterval(this.interval), this.options.interval && !this.paused && (this.interval = setInterval(e.proxy(this.next, this), this.options.interval)), this
		},
		getActiveIndex: function() {
			return this.$active = this.$element.find(".item.active"), this.$items = this.$active.parent().children(), this.$items.index(this.$active)
		},
		to: function(t) {
			var i = this.getActiveIndex(),
				n = this;
			if(!(t > this.$items.length - 1 || 0 > t)) return this.sliding ? this.$element.one("slid", function() {
				n.to(t)
			}) : i == t ? this.pause().cycle() : this.slide(t > i ? "next" : "prev", e(this.$items[t]))
		},
		pause: function(t) {
			return t || (this.paused = !0), this.$element.find(".next, .prev").length && e.support.transition.end && (this.$element.trigger(e.support.transition.end), this.cycle(!0)), clearInterval(this.interval), this.interval = null, this
		},
		next: function() {
			return this.sliding ? void 0 : this.slide("next")
		},
		prev: function() {
			return this.sliding ? void 0 : this.slide("prev")
		},
		slide: function(t, i) {
			var n, r = this.$element.find(".item.active"),
				o = i || r[t](),
				s = this.interval,
				a = "next" == t ? "left" : "right",
				l = "next" == t ? "first" : "last",
				c = this;
			if(this.sliding = !0, s && this.pause(), o = o.length ? o : this.$element.find(".item")[l](), n = e.Event("slide", {
					relatedTarget: o[0],
					direction: a
				}), !o.hasClass("active")) {
				if(this.$indicators.length && (this.$indicators.find(".active").removeClass("active"), this.$element.one("slid", function() {
						var t = e(c.$indicators.children()[c.getActiveIndex()]);
						t && t.addClass("active")
					})), e.support.transition && this.$element.hasClass("slide")) {
					if(this.$element.trigger(n), n.isDefaultPrevented()) return;
					o.addClass(t), o[0].offsetWidth, r.addClass(a), o.addClass(a), this.$element.one(e.support.transition.end, function() {
						o.removeClass([t, a].join(" ")).addClass("active"), r.removeClass(["active", a].join(" ")), c.sliding = !1, setTimeout(function() {
							c.$element.trigger("slid")
						}, 0)
					})
				} else {
					if(this.$element.trigger(n), n.isDefaultPrevented()) return;
					r.removeClass("active"), o.addClass("active"), this.sliding = !1, this.$element.trigger("slid")
				}
				return s && this.cycle(), this
			}
		}
	};
	var i = e.fn.carousel;
	e.fn.carousel = function(i) {
		return this.each(function() {
			var n = e(this),
				r = n.data("carousel"),
				o = e.extend({}, e.fn.carousel.defaults, "object" == typeof i && i),
				s = "string" == typeof i ? i : o.slide;
			r || n.data("carousel", r = new t(this, o)), "number" == typeof i ? r.to(i) : s ? r[s]() : o.interval && r.pause().cycle()
		})
	}, e.fn.carousel.defaults = {
		interval: 5e3,
		pause: "hover"
	}, e.fn.carousel.Constructor = t, e.fn.carousel.noConflict = function() {
		return e.fn.carousel = i, this
	}, e(document).on("click.carousel.data-api", "[data-slide], [data-slide-to]", function(t) {
		var i, n, r = e(this),
			o = e(r.attr("data-target") || (i = r.attr("href")) && i.replace(/.*(?=#[^\s]+$)/, "")),
			s = e.extend({}, o.data(), r.data());
		o.carousel(s), (n = r.attr("data-slide-to")) && o.data("carousel").pause().to(n).cycle(), t.preventDefault()
	})
}(window.jQuery);