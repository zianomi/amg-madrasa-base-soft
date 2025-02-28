function createRequestObject() {
    var e = !1;
    if (window.XMLHttpRequest) e = new XMLHttpRequest, e.overrideMimeType; else if (window.ActiveXObject)try {
        e = new ActiveXObject("Msxml2.XMLHTTP")
    } catch (t) {
        try {
            e = new ActiveXObject("Microsoft.XMLHTTP")
        } catch (t) {
        }
    }
    return e ? e : (alert("Cannot create XMLHTTP instance"), !1)
}

function sndUpdateReq(e) {
    http.open("get", e), http.onreadystatechange = handleUpdateResponse, http.send(null)
}
function getThisPage() {
    var e = this_page, t = "?";
    return -1 != e.indexOf("?") && (t = "&"), e += t
}
function sndDeleteReq(e) {
    http.open("get", e), http.onreadystatechange = function () {
        if (4 == http.readyState) {
            var e = http.responseText, t = e.split("|"), n = t[0], i = t[1];
            $("tr[id^=" + n + "_row_" + i + "]").fadeOut("slow"), updateRowCount(n)
        }
    }, http.send(null)
}



function sndAddReq(e, t) {
    http.open("get", e), http.onreadystatechange = function () {
        if (4 == http.readyState) {
            var e = "?";
            ajax_file.search("[?]") > 0 && (e = "&");
            var n = ajax_file + e + "&table=" + t;
            add_http.open("get", n), add_http.onreadystatechange = function () {
                if (4 == add_http.readyState) {
                    var e = add_http.responseText, n = e;

                    if(add_http.responseText == "OK"){
                        $("#ajaxcrud_succ").show().text("Record Added");
                        setTimeout(
                          function()
                          {
                              location.reload();
                          }, 2000);
                    }
                    else{
                        $("#ajaxcrud_error").show().text(add_http.responseText);
                    }
                    //var ziaData = ajaxcrudLoadData(ajax_file,"amg_ajax_crud_show_add_table="+t);
                    //document.getElementById(t).innerHTML = n, doValidation(), updateRowCount(t)
                    //document.getElementById(t).innerHTML = ziaData;
                }
            }//, add_http.send(null)
        }
    }, http.send(null)
}

function sndFilterReq(e, t) {
    http.open("get", e), http.onreadystatechange = function () {
        if (4 == http.readyState) {
            var e = "?";
            ajax_file.search("[?]") > 0 && (e = "&"), filter_http.open("get", ajax_file + e + "ajaxAction=filter&table=" + t), filter_http.onreadystatechange = function () {
                if (4 == filter_http.readyState) {
                    var e = filter_http.responseText;
                    //location.reload();
                    document.getElementById(t).innerHTML = e, doValidation(), updateRowCount(t)
                }
            }, filter_http.send(null)
        }
    }, http.send(null)
}
function sndSortReq(e, t) {
    http.open("get", e), http.onreadystatechange = function () {
        if (4 == http.readyState) {
            var e = "?";
            ajax_file.search("[?]") > 0 && (e = "&");
            var n = ajax_file + e + "ajaxAction=sort&table=" + t;
            sort_http.open("get", n), sort_http.onreadystatechange = function () {
                if (4 == sort_http.readyState) {
                    var e = sort_http.responseText;
                    document.getElementById(t).innerHTML = e, doValidation()
                }
            }, sort_http.send(null)
        }
    }, http.send(null)
}
function updateRowCount(e) {
    var t = "?";
    ajax_file.search("[?]") > 0 && (t = "&"), http.open("get", ajax_file + t + "ajaxAction=getRowCount&table=" + e), http.onreadystatechange = function () {
        if (4 == http.readyState) {
            var t = http.responseText;
            $("." + e + "_rowCount").html(t)
        }
    }, http.send(null)
}
function sndReqNoResponse(e) {
    http.open("get", e), http.onreadystatechange = doNothing, http.send(null)
}
function sndReqNoResponseChk(e) {
    http.open("get", e, !1), http.onreadystatechange = doNothing, http.send(null)
}
function doNothing() {
}


function setLoadingImage(e) {
    document.getElementById(e).innerHTML = loading_image_html
}



function filterTable(e, t, n, i) {
    var a = getFormValues(document.getElementById(t + "_filter_form"), "");
    var s = "";
    if ("" != a) {
        s = getThisPage() + a + "&table=" + t + "&" + i;
        filterReq = "&table=" + t + "&" + a + "&" + i
        console.log(filterReq);
    } else {
        s = getThisPage() + "action=unfilter";
        filterReq = "&action=unfilter"
    }
    var r = function () {
        setLoadingImage(t), sndFilterReq(s, t)
    };
    e.zid && clearTimeout(e.zid), e.zid = setTimeout(r, 1200)
}
function confirmDelete(e, t, n) {
    confirm(deleteMessageText) && ajax_deleteRow(e, t, n)
}

function ajax_deleteRow(e, t, n) {
    var i = "?";
    ajax_file.search("[?]") > 0 && (i = "&");
    var a = ajax_file + i + "ajaxAction=delete&id=" + e + "&table=" + t + "&pk=" + n;
    sndDeleteReq(a)
}
function handleUpdateResponse() {
    if (4 == http.readyState) {
        var e = http.responseText;
        if ("error" == e.substring(0, 5)) {
            var t = e.split("|"), n = t[1], i = t[2];
            document.getElementById(n + "_show").innerHTML = i, document.getElementById(n + "_show").style.display = "", document.getElementById(n + "_edit").style.display = "none", document.getElementById(n + "_save").style.display = "none"
        } else {
            var t = e.split("|"), n = t[0], a = myStripSlashes(t[1]);
            if ("{selectbox}" != a) document.getElementById(n + "_show").innerHTML = null != a ? a : ""; else {
                var s = document.getElementById("dropdown_" + n);
                document.getElementById(n + "_show").innerHTML = s.options[s.selectedIndex].text
            }
            document.getElementById(n + "_show").style.display = "", document.getElementById(n + "_edit").style.display = "none", document.getElementById(n + "_save").style.display = "none"
        }
    }
}
function getFormValues(fobj, valFunc) {
    for (var str = "", valueArr = null, val = "", cmd = "", element_type, i = 0; i < fobj.elements.length; i++)if (element_type = fobj.elements[i].type, "text" == element_type || "textarea" == element_type) valFunc && (cmd = valFunc + "(fobj.elements[i].value)", val = eval(cmd)), str += fobj.elements[i].name + "=" + cleanseStrForURIEncode(myAddSlashes(fobj.elements[i].value)) + "&"; else if ("select-one" == element_type) str += fobj.elements[i].name + "=" + fobj.elements[i].options[fobj.elements[i].selectedIndex].value + "&"; else if ("checkbox" == element_type) {
        var chkValue = "";
        if (fobj.elements[i].checked)var chkValue = cleanseStrForURIEncode(fobj.elements[i].value);
        str += fobj.elements[i].name + "=" + chkValue + "&"
    }
    return str = str.substr(0, str.length - 1)
}
function clearForm(e) {
    var t, n, i, a;
    if (t = document.getElementById ? document.getElementById(e) : document.forms[e], document.getElementsByTagName) {
        for (n = t.getElementsByTagName("input"), i = 0, a; a = n.item(i++);)"text" == a.getAttribute("type") ? a.value = "" : "checkbox" == a.getAttribute("type") && (a.checked = !1);
        for (n = t.getElementsByTagName("select"), i = 0, a; a = n.item(i++);)a.options.selectedIndex = 0;
        for (n = t.getElementsByTagName("textarea"), i = 0, a; a = n.item(i++);)a.value = ""
    } else for (n = t.elements, i = 0, a; a = n[i++];)"text" == a.type && (a.value = "")
}
function fn_validateNumeric(e, t, n) {
    var i = t.value, a = e.which, s = e.keyCode;
    return null == a && (a = s), a >= 48 && 57 >= a || 8 == s || 9 == s || 37 == s || 39 == s || 46 == s || 46 == a || 13 == a || 45 == a || 35 == s || 36 == s ? "n" == n && 46 == a ? !1 : -1 != i.indexOf(".") && 46 == a ? !1 : !0 : !1
}
function cleanseStrForURIEncode(e) {
    return e = encodeURI(e), e = e.replace(/#/g, "%23"), e = e.replace(/&/g, "%26"), e = e.replace(/>/g, "&gt;"), e = e.replace(/</g, "&lt;"), e = e.replace(/"/g, "&quot;")
}
function myAddSlashes(e) {
    return e = e.replace(/\"/g, '\\"')
}
function myStripSlashes(e) {
    return e = e.replace(/\\'/g, "'"), e = e.replace(/\\"/g, '"')
}
function hover(e) {
    e.style.backgroundColor = "#FFFF99"
}
function unHover(e) {
    e.className = ""
}
function setAllCheckboxes(e, t) {
    for (var n = document.getElementsByName(e), i = 0; i < n.length; i++)n[i].checked == t && (n[i].checked = t, n[i].click())
}
function doValidation() {
    try {
        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            showOn: "button",
            buttonImage: "calendar.gif",
            buttonImageOnly: !0,
            onClose: function () {
                this.focus()
            }
        })
    } catch (e) {
    }
}
var deleteMessageText = "Are you sure you want to delete this item from the database? This cannot be undone.",
    loading_image_html, filterReq = "", pageReq = "", sortReq = "", this_page, http = createRequestObject(),
    add_http = createRequestObject(), filter_http = createRequestObject(), sort_http = createRequestObject(),
    other_http = createRequestObject(), prior_class = "";
Array.prototype.findIndex = function (e) {
    for (var t = "", n = 0; n < this.length; n++)if (this[n] == e)return n;
    return t
}, "function" != typeof Array.prototype.splice && (Array.prototype.splice = function (e, t) {
    e = +e || 0;
    var n, i, a = [], s = this.length, r = Math.min(arguments.length - 2, 0);
    for (e = 0 > e ? Math.max(e + s, 0) : Math.min(e, s), t = Math.min(Math.max(+t || 0, 0), s - e), n = 0; t > n; ++n)a[n] = this[e + n];
    if (t > r)for (n = e, i = s - t; i > n; ++n)this[n + r] = this[n + t]; else if (r > t)for (n = s - 1, i = e + t; n >= i; --n)this[n + r - t] = this[n];
    for (n = e, i = 2; r > i; ++n, ++i)this[n] = arguments[i];
    return this.length = s - t + r, a
}), function (e) {
    function t() {
        var e = document.createElement("input"), t = "onpaste";
        return e.setAttribute(t, ""), "function" == typeof e[t] ? "paste" : "input"
    }

    var n, i = t() + ".mask", a = navigator.userAgent, s = /iphone/i.test(a), r = /android/i.test(a);
    e.mask = {
        definitions: {9: "[0-9]", a: "[A-Za-z]", "*": "[A-Za-z0-9]"},
        dataName: "rawMaskFn",
        placeholder: "_"
    }, e.fn.extend({
        caret: function (e, t) {
            var n;
            if (0 !== this.length && !this.is(":hidden"))return "number" == typeof e ? (t = "number" == typeof t ? t : e, this.each(function () {
                this.setSelectionRange ? this.setSelectionRange(e, t) : this.createTextRange && (n = this.createTextRange(), n.collapse(!0), n.moveEnd("character", t), n.moveStart("character", e), n.select())
            })) : (this[0].setSelectionRange ? (e = this[0].selectionStart, t = this[0].selectionEnd) : document.selection && document.selection.createRange && (n = document.selection.createRange(), e = 0 - n.duplicate().moveStart("character", -1e5), t = e + n.text.length), {
                begin: e,
                end: t
            })
        }, unmask: function () {
            return this.trigger("unmask")
        }, mask: function (t, a) {
            var o, u, l, d, h, c;
            return !t && this.length > 0 ? (o = e(this[0]), o.data(e.mask.dataName)()) : (a = e.extend({
                placeholder: e.mask.placeholder,
                completed: null
            }, a), u = e.mask.definitions, l = [], d = c = t.length, h = null, e.each(t.split(""), function (e, t) {
                "?" == t ? (c--, d = e) : u[t] ? (l.push(new RegExp(u[t])), null === h && (h = l.length - 1)) : l.push(null)
            }), this.trigger("unmask").each(function () {
                function o(e) {
                    for (; ++e < c && !l[e];);
                    return e
                }

                function m(e) {
                    for (; --e >= 0 && !l[e];);
                    return e
                }

                function f(e, t) {
                    var n, i;
                    if (!(0 > e)) {
                        for (n = e, i = o(t); c > n; n++)if (l[n]) {
                            if (!(c > i && l[n].test(D[i])))break;
                            D[n] = D[i], D[i] = a.placeholder, i = o(i)
                        }
                        b(), x.caret(Math.max(h, e))
                    }
                }

                function g(e) {
                    var t, n, i, s;
                    for (t = e, n = a.placeholder; c > t; t++)if (l[t]) {
                        if (i = o(t), s = D[t], D[t] = n, !(c > i && l[i].test(s)))break;
                        n = s
                    }
                }

                function p(e) {
                    var t, n, i, a = e.which;
                    8 === a || 46 === a || s && 127 === a ? (t = x.caret(), n = t.begin, i = t.end, i - n === 0 && (n = 46 !== a ? m(n) : i = o(n - 1), i = 46 === a ? o(i) : i), v(n, i), f(n, i - 1), e.preventDefault()) : 27 == a && (x.val(E), x.caret(0, y()), e.preventDefault())
                }

                function F(t) {
                    var n, i, s, u = t.which, d = x.caret();
                    t.ctrlKey || t.altKey || t.metaKey || 32 > u || u && (d.end - d.begin !== 0 && (v(d.begin, d.end), f(d.begin, d.end - 1)), n = o(d.begin - 1), c > n && (i = String.fromCharCode(u), l[n].test(i) && (g(n), D[n] = i, b(), s = o(n), r ? setTimeout(e.proxy(e.fn.caret, x, s), 0) : x.caret(s), a.completed && s >= c && a.completed.call(x))), t.preventDefault())
                }

                function v(e, t) {
                    var n;
                    for (n = e; t > n && c > n; n++)l[n] && (D[n] = a.placeholder)
                }

                function b() {
                    x.val(D.join(""))
                }

                function y(e) {
                    var t, n, i = x.val(), s = -1;
                    for (t = 0, pos = 0; c > t; t++)if (l[t]) {
                        for (D[t] = a.placeholder; pos++ < i.length;)if (n = i.charAt(pos - 1), l[t].test(n)) {
                            D[t] = n, s = t;
                            break
                        }
                        if (pos > i.length)break
                    } else D[t] === i.charAt(pos) && t !== d && (pos++, s = t);
                    return e ? b() : d > s + 1 ? (x.val(""), v(0, c)) : (b(), x.val(x.val().substring(0, s + 1))), d ? t : h
                }

                var x = e(this), D = e.map(t.split(""), function (e) {
                    return "?" != e ? u[e] ? a.placeholder : e : void 0
                }), E = x.val();
                x.data(e.mask.dataName, function () {
                    return e.map(D, function (e, t) {
                        return l[t] && e != a.placeholder ? e : null
                    }).join("")
                }), x.attr("readonly") || x.one("unmask", function () {
                    x.unbind(".mask").removeData(e.mask.dataName)
                }).bind("focus.mask", function () {
                    clearTimeout(n);
                    var e;
                    E = x.val(), e = y(), n = setTimeout(function () {
                        b(), e == t.length ? x.caret(0, e) : x.caret(e)
                    }, 10)
                }).bind("blur.mask", function () {
                    y(), x.val() != E && x.change()
                }).bind("keydown.mask", p).bind("keypress.mask", F).bind(i, function () {
                    setTimeout(function () {
                        var e = y(!0);
                        x.caret(e), a.completed && e == x.val().length && a.completed.call(x)
                    }, 0)
                }), y()
            }))
        }
    })
}(jQuery), function (e) {
    e.extend(e.fn, {
        validate: function (t) {
            if (!this.length)return void(t && t.debug && window.console && console.warn("nothing selected, can't validate, returning nothing"));
            var n = e.data(this[0], "validator");
            return n ? n : (n = new e.validator(t, this[0]), e.data(this[0], "validator", n), n.settings.onsubmit && (this.find("input, button").filter(".cancel").click(function () {
                n.cancelSubmit = !0
            }), n.settings.submitHandler && this.find("input, button").filter(":submit").click(function () {
                n.submitButton = this
            }), this.submit(function (t) {
                function i() {
                    if (n.settings.submitHandler) {
                        if (n.submitButton)var t = e("<input type='hidden'/>").attr("name", n.submitButton.name).val(n.submitButton.value).appendTo(n.currentForm);
                        return n.settings.submitHandler.call(n, n.currentForm), n.submitButton && t.remove(), !1
                    }
                    return !0
                }

                return n.settings.debug && t.preventDefault(), n.cancelSubmit ? (n.cancelSubmit = !1, i()) : n.form() ? n.pendingRequest ? (n.formSubmitted = !0, !1) : i() : (n.focusInvalid(), !1)
            })), n)
        }, valid: function () {
            if (e(this[0]).is("form"))return this.validate().form();
            var t = !0, n = e(this[0].form).validate();
            return this.each(function () {
                t &= n.element(this)
            }), t
        }, removeAttrs: function (t) {
            var n = {}, i = this;
            return e.each(t.split(/\s/), function (e, t) {
                n[t] = i.attr(t), i.removeAttr(t)
            }), n
        }, rules: function (t, n) {
            var i = this[0];
            if (t) {
                var a = e.data(i.form, "validator").settings, s = a.rules, r = e.validator.staticRules(i);
                switch (t) {
                    case"add":
                        e.extend(r, e.validator.normalizeRule(n)), s[i.name] = r, n.messages && (a.messages[i.name] = e.extend(a.messages[i.name], n.messages));
                        break;
                    case"remove":
                        if (!n)return delete s[i.name], r;
                        var o = {};
                        return e.each(n.split(/\s/), function (e, t) {
                            o[t] = r[t], delete r[t]
                        }), o
                }
            }
            var u = e.validator.normalizeRules(e.extend({}, e.validator.metadataRules(i), e.validator.classRules(i), e.validator.attributeRules(i), e.validator.staticRules(i)), i);
            if (u.required) {
                var l = u.required;
                delete u.required, u = e.extend({required: l}, u)
            }
            return u
        }
    }), e.extend(e.expr[":"], {
        blank: function (t) {
            return !e.trim("" + t.value)
        }, filled: function (t) {
            return !!e.trim("" + t.value)
        }, unchecked: function (e) {
            return !e.checked
        }
    }), e.validator = function (t, n) {
        this.settings = e.extend(!0, {}, e.validator.defaults, t), this.currentForm = n, this.init()
    }, e.validator.format = function (t, n) {
        return 1 == arguments.length ? function () {
            var n = e.makeArray(arguments);
            return n.unshift(t), e.validator.format.apply(this, n)
        } : (arguments.length > 2 && n.constructor != Array && (n = e.makeArray(arguments).slice(1)), n.constructor != Array && (n = [n]), e.each(n, function (e, n) {
            t = t.replace(new RegExp("\\{" + e + "\\}", "g"), n)
        }), t)
    }, e.extend(e.validator, {
        defaults: {
            messages: {},
            groups: {},
            rules: {},
            errorClass: "alert alert-error",
            validClass: "valid",
            errorElement: "label",
            focusInvalid: !0,
            errorContainer: e([]),
            errorLabelContainer: e([]),
            onsubmit: !0,
            ignore: [],
            ignoreTitle: !1,
            onfocusin: function (e) {
                this.lastActive = e, this.settings.focusCleanup && !this.blockFocusCleanup && (this.settings.unhighlight && this.settings.unhighlight.call(this, e, this.settings.errorClass, this.settings.validClass), this.errorsFor(e).hide())
            },
            onfocusout: function (e) {
                this.checkable(e) || !(e.name in this.submitted) && this.optional(e) || this.element(e)
            },
            onkeyup: function (e) {
                (e.name in this.submitted || e == this.lastElement) && this.element(e)
            },
            onclick: function (e) {
                e.name in this.submitted ? this.element(e) : e.parentNode.name in this.submitted && this.element(e.parentNode)
            },
            highlight: function (t, n, i) {
                e(t).addClass(n).removeClass(i)
            },
            unhighlight: function (t, n, i) {
                e(t).removeClass(n).addClass(i)
            }
        },
        setDefaults: function (t) {
            e.extend(e.validator.defaults, t)
        },
        messages: {
            required: "This field is required.",
            remote: "Please fix this field.",
            email: "Please enter a valid email address.",
            url: "Please enter a valid URL.",
            date: "Please enter a valid date.",
            dateISO: "Please enter a valid date (ISO).",
            number: "Please enter a valid number.",
            digits: "Please enter only digits.",
            creditcard: "Please enter a valid credit card number.",
            equalTo: "Please enter the same value again.",
            accept: "Please enter a value with a valid extension.",
            maxlength: e.validator.format("Please enter no more than {0} characters."),
            minlength: e.validator.format("Please enter at least {0} characters."),
            rangelength: e.validator.format("Please enter a value between {0} and {1} characters long."),
            range: e.validator.format("Please enter a value between {0} and {1}."),
            max: e.validator.format("Please enter a value less than or equal to {0}."),
            min: e.validator.format("Please enter a value greater than or equal to {0}.")
        },
        autoCreateRanges: !1,
        prototype: {
            init: function () {
                function t(t) {
                    var n = e.data(this[0].form, "validator"), i = "on" + t.type.replace(/^validate/, "");
                    n.settings[i] && n.settings[i].call(n, this[0])
                }

                this.labelContainer = e(this.settings.errorLabelContainer), this.errorContext = this.labelContainer.length && this.labelContainer || e(this.currentForm), this.containers = e(this.settings.errorContainer).add(this.settings.errorLabelContainer), this.submitted = {}, this.valueCache = {}, this.pendingRequest = 0, this.pending = {}, this.invalid = {}, this.reset();
                var n = this.groups = {};
                e.each(this.settings.groups, function (t, i) {
                    e.each(i.split(/\s/), function (e, i) {
                        n[i] = t
                    })
                });
                var i = this.settings.rules;
                e.each(i, function (t, n) {
                    i[t] = e.validator.normalizeRule(n)
                }), e(this.currentForm).validateDelegate(":text, :password, :file, select, textarea", "focusin focusout keyup", t).validateDelegate(":radio, :checkbox, select, option", "click", t), this.settings.invalidHandler && e(this.currentForm).bind("invalid-form.validate", this.settings.invalidHandler)
            }, form: function () {
                return this.checkForm(), e.extend(this.submitted, this.errorMap), this.invalid = e.extend({}, this.errorMap), this.valid() || e(this.currentForm).triggerHandler("invalid-form", [this]), this.showErrors(), this.valid()
            }, checkForm: function () {
                this.prepareForm();
                for (var e = 0, t = this.currentElements = this.elements(); t[e]; e++)this.check(t[e]);
                return this.valid()
            }, element: function (t) {
                t = this.clean(t), this.lastElement = t, this.prepareElement(t), this.currentElements = e(t);
                var n = this.check(t);
                return n ? delete this.invalid[t.name] : this.invalid[t.name] = !0, this.numberOfInvalids() || (this.toHide = this.toHide.add(this.containers)), this.showErrors(), n
            }, showErrors: function (t) {
                if (t) {
                    e.extend(this.errorMap, t), this.errorList = [];
                    for (var n in t)this.errorList.push({message: t[n], element: this.findByName(n)[0]});
                    this.successList = e.grep(this.successList, function (e) {
                        return !(e.name in t)
                    })
                }
                this.settings.showErrors ? this.settings.showErrors.call(this, this.errorMap, this.errorList) : this.defaultShowErrors()
            }, resetForm: function () {
                e.fn.resetForm && e(this.currentForm).resetForm(), this.submitted = {}, this.prepareForm(), this.hideErrors(), this.elements().removeClass(this.settings.errorClass)
            }, numberOfInvalids: function () {
                return this.objectLength(this.invalid)
            }, objectLength: function (e) {
                var t = 0;
                for (var n in e)t++;
                return t
            }, hideErrors: function () {
                this.addWrapper(this.toHide).hide()
            }, valid: function () {
                return 0 == this.size()
            }, size: function () {
                return this.errorList.length
            }, focusInvalid: function () {
                if (this.settings.focusInvalid)try {
                    e(this.findLastActive() || this.errorList.length && this.errorList[0].element || []).filter(":visible").focus().trigger("focusin")
                } catch (t) {
                }
            }, findLastActive: function () {
                var t = this.lastActive;
                return t && 1 == e.grep(this.errorList, function (e) {
                        return e.element.name == t.name
                    }).length && t
            }, elements: function () {
                var t = this, n = {};
                return e([]).add(this.currentForm.elements).filter(":input").not(":submit, :reset, :image, [disabled]").not(this.settings.ignore).filter(function () {
                    return !this.name && t.settings.debug && window.console && console.error("%o has no name assigned", this), this.name in n || !t.objectLength(e(this).rules()) ? !1 : (n[this.name] = !0, !0)
                })
            }, clean: function (t) {
                return e(t)[0]
            }, errors: function () {
                return e(this.settings.errorElement + "." + this.settings.errorClass, this.errorContext)
            }, reset: function () {
                this.successList = [], this.errorList = [], this.errorMap = {}, this.toShow = e([]), this.toHide = e([]), this.currentElements = e([])
            }, prepareForm: function () {
                this.reset(), this.toHide = this.errors().add(this.containers)
            }, prepareElement: function (e) {
                this.reset(), this.toHide = this.errorsFor(e)
            }, check: function (t) {
                t = this.clean(t), this.checkable(t) && (t = this.findByName(t.name)[0]);
                var n = e(t).rules(), i = !1;
                for (method in n) {
                    var a = {method: method, parameters: n[method]};
                    try {
                        var s = e.validator.methods[method].call(this, t.value.replace(/\r/g, ""), t, a.parameters);
                        if ("dependency-mismatch" == s) {
                            i = !0;
                            continue
                        }
                        if (i = !1, "pending" == s)return void(this.toHide = this.toHide.not(this.errorsFor(t)));
                        if (!s)return this.formatAndAdd(t, a), !1
                    } catch (r) {
                        throw this.settings.debug && window.console && console.log("exception occured when checking element " + t.id + ", check the '" + a.method + "' method", r), r
                    }
                }
                return i ? void 0 : (this.objectLength(n) && this.successList.push(t), !0)
            }, customMetaMessage: function (t, n) {
                if (e.metadata) {
                    var i = this.settings.meta ? e(t).metadata()[this.settings.meta] : e(t).metadata();
                    return i && i.messages && i.messages[n]
                }
            }, customMessage: function (e, t) {
                var n = this.settings.messages[e];
                return n && (n.constructor == String ? n : n[t])
            }, findDefined: function () {
                for (var e = 0; e < arguments.length; e++)if (void 0 !== arguments[e])return arguments[e];
                return void 0
            }, defaultMessage: function (t, n) {
                return this.findDefined(this.customMessage(t.name, n), this.customMetaMessage(t, n), !this.settings.ignoreTitle && t.title || void 0, e.validator.messages[n], "<strong>Warning: No message defined for " + t.name + "</strong>")
            }, formatAndAdd: function (e, t) {
                var n = this.defaultMessage(e, t.method), i = /\$?\{(\d+)\}/g;
                "function" == typeof n ? n = n.call(this, t.parameters, e) : i.test(n) && (n = jQuery.format(n.replace(i, "{$1}"), t.parameters)), this.errorList.push({
                    message: n,
                    element: e
                }), this.errorMap[e.name] = n, this.submitted[e.name] = n
            }, addWrapper: function (e) {
                return this.settings.wrapper && (e = e.add(e.parent(this.settings.wrapper))), e
            }, defaultShowErrors: function () {
                for (var e = 0; this.errorList[e]; e++) {
                    var t = this.errorList[e];
                    this.settings.highlight && this.settings.highlight.call(this, t.element, this.settings.errorClass, this.settings.validClass), this.showLabel(t.element, t.message)
                }
                if (this.errorList.length && (this.toShow = this.toShow.add(this.containers)), this.settings.success)for (var e = 0; this.successList[e]; e++)this.showLabel(this.successList[e]);
                if (this.settings.unhighlight)for (var e = 0, n = this.validElements(); n[e]; e++)this.settings.unhighlight.call(this, n[e], this.settings.errorClass, this.settings.validClass);
                this.toHide = this.toHide.not(this.toShow), this.hideErrors(), this.addWrapper(this.toShow).show()
            }, validElements: function () {
                return this.currentElements.not(this.invalidElements())
            }, invalidElements: function () {
                return e(this.errorList).map(function () {
                    return this.element
                })
            }, showLabel: function (t, n) {
                var i = this.errorsFor(t);
                i.length ? (i.removeClass().addClass(this.settings.errorClass), i.attr("generated") && i.html(n)) : (i = e("<" + this.settings.errorElement + "/>").attr({
                    "for": this.idOrName(t),
                    generated: !0
                }).addClass(this.settings.errorClass).html(n || ""), this.settings.wrapper && (i = i.hide().show().wrap("<" + this.settings.wrapper + "/>").parent()), this.labelContainer.append(i).length || (this.settings.errorPlacement ? this.settings.errorPlacement(i, e(t)) : i.insertAfter(t))), !n && this.settings.success && (i.text(""), "string" == typeof this.settings.success ? i.addClass(this.settings.success) : this.settings.success(i)), this.toShow = this.toShow.add(i)
            }, errorsFor: function (t) {
                var n = this.idOrName(t);
                return this.errors().filter(function () {
                    return e(this).attr("for") == n
                })
            }, idOrName: function (e) {
                return this.groups[e.name] || (this.checkable(e) ? e.name : e.id || e.name)
            }, checkable: function (e) {
                return /radio|checkbox/i.test(e.type)
            }, findByName: function (t) {
                var n = this.currentForm;
                return e(document.getElementsByName(t)).map(function (e, i) {
                    return i.form == n && i.name == t && i || null
                })
            }, getLength: function (t, n) {
                switch (n.nodeName.toLowerCase()) {
                    case"select":
                        return e("option:selected", n).length;
                    case"input":
                        if (this.checkable(n))return this.findByName(n.name).filter(":checked").length
                }
                return t.length
            }, depend: function (e, t) {
                return this.dependTypes[typeof e] ? this.dependTypes[typeof e](e, t) : !0
            }, dependTypes: {
                "boolean": function (e) {
                    return e
                }, string: function (t, n) {
                    return !!e(t, n.form).length
                }, "function": function (e, t) {
                    return e(t)
                }
            }, optional: function (t) {
                return !e.validator.methods.required.call(this, e.trim(t.value), t) && "dependency-mismatch"
            }, startRequest: function (e) {
                this.pending[e.name] || (this.pendingRequest++, this.pending[e.name] = !0)
            }, stopRequest: function (t, n) {
                this.pendingRequest--, this.pendingRequest < 0 && (this.pendingRequest = 0), delete this.pending[t.name], n && 0 == this.pendingRequest && this.formSubmitted && this.form() ? (e(this.currentForm).submit(), this.formSubmitted = !1) : !n && 0 == this.pendingRequest && this.formSubmitted && (e(this.currentForm).triggerHandler("invalid-form", [this]), this.formSubmitted = !1)
            }, previousValue: function (t) {
                return e.data(t, "previousValue") || e.data(t, "previousValue", {
                        old: null,
                        valid: !0,
                        message: this.defaultMessage(t, "remote")
                    })
            }
        },
        classRuleSettings: {
            required: {required: !0},
            email: {email: !0},
            url: {url: !0},
            date: {date: !0},
            dateISO: {dateISO: !0},
            dateDE: {dateDE: !0},
            number: {number: !0},
            numberDE: {numberDE: !0},
            digits: {digits: !0},
            creditcard: {creditcard: !0}
        },
        addClassRules: function (t, n) {
            t.constructor == String ? this.classRuleSettings[t] = n : e.extend(this.classRuleSettings, t)
        },
        classRules: function (t) {
            var n = {}, i = e(t).attr("class");
            return i && e.each(i.split(" "), function () {
                this in e.validator.classRuleSettings && e.extend(n, e.validator.classRuleSettings[this])
            }), n
        },
        attributeRules: function (t) {
            var n = {}, i = e(t);
            for (method in e.validator.methods) {
                var a = i.attr(method);
                a && (n[method] = a)
            }
            return n.maxlength && /-1|2147483647|524288/.test(n.maxlength) && delete n.maxlength, n
        },
        metadataRules: function (t) {
            if (!e.metadata)return {};
            var n = e.data(t.form, "validator").settings.meta;
            return n ? e(t).metadata()[n] : e(t).metadata()
        },
        staticRules: function (t) {
            var n = {}, i = e.data(t.form, "validator");
            return i.settings.rules && (n = e.validator.normalizeRule(i.settings.rules[t.name]) || {}), n
        },
        normalizeRules: function (t, n) {
            return e.each(t, function (i, a) {
                if (a === !1)return void delete t[i];
                if (a.param || a.depends) {
                    var s = !0;
                    switch (typeof a.depends) {
                        case"string":
                            s = !!e(a.depends, n.form).length;
                            break;
                        case"function":
                            s = a.depends.call(n, n)
                    }
                    s ? t[i] = void 0 !== a.param ? a.param : !0 : delete t[i]
                }
            }), e.each(t, function (i, a) {
                t[i] = e.isFunction(a) ? a(n) : a
            }), e.each(["minlength", "maxlength", "min", "max"], function () {
                t[this] && (t[this] = Number(t[this]))
            }), e.each(["rangelength", "range"], function () {
                t[this] && (t[this] = [Number(t[this][0]), Number(t[this][1])])
            }), e.validator.autoCreateRanges && (t.min && t.max && (t.range = [t.min, t.max], delete t.min, delete t.max), t.minlength && t.maxlength && (t.rangelength = [t.minlength, t.maxlength], delete t.minlength, delete t.maxlength)), t.messages && delete t.messages, t
        },
        normalizeRule: function (t) {
            if ("string" == typeof t) {
                var n = {};
                e.each(t.split(/\s/), function () {
                    n[this] = !0
                }), t = n
            }
            return t
        },
        addMethod: function (t, n, i) {
            e.validator.methods[t] = n, e.validator.messages[t] = void 0 != i ? i : e.validator.messages[t], n.length < 3 && e.validator.addClassRules(t, e.validator.normalizeRule(t))
        },
        methods: {
            required: function (t, n, i) {
                if (!this.depend(i, n))return "dependency-mismatch";
                switch (n.nodeName.toLowerCase()) {
                    case"select":
                        var a = e(n).val();
                        return a && a.length > 0;
                    case"input":
                        if (this.checkable(n))return this.getLength(t, n) > 0;
                    default:
                        return e.trim(t).length > 0
                }
            }, remote: function (t, n, i) {
                if (this.optional(n))return "dependency-mismatch";
                var a = this.previousValue(n);
                if (this.settings.messages[n.name] || (this.settings.messages[n.name] = {}), a.originalMessage = this.settings.messages[n.name].remote, this.settings.messages[n.name].remote = a.message, i = "string" == typeof i && {url: i} || i, a.old !== t) {
                    a.old = t;
                    var s = this;
                    this.startRequest(n);
                    var r = {};
                    return r[n.name] = t, e.ajax(e.extend(!0, {
                        url: i,
                        mode: "abort",
                        port: "validate" + n.name,
                        dataType: "json",
                        data: r,
                        success: function (i) {
                            s.settings.messages[n.name].remote = a.originalMessage;
                            var r = i === !0;
                            if (r) {
                                var o = s.formSubmitted;
                                s.prepareElement(n), s.formSubmitted = o, s.successList.push(n), s.showErrors()
                            } else {
                                var u = {}, l = a.message = i || s.defaultMessage(n, "remote");
                                u[n.name] = e.isFunction(l) ? l(t) : l, s.showErrors(u)
                            }
                            a.valid = r, s.stopRequest(n, r)
                        }
                    }, i)), "pending"
                }
                return this.pending[n.name] ? "pending" : a.valid
            }, minlength: function (t, n, i) {
                return this.optional(n) || this.getLength(e.trim(t), n) >= i
            }, maxlength: function (t, n, i) {
                return this.optional(n) || this.getLength(e.trim(t), n) <= i
            }, rangelength: function (t, n, i) {
                var a = this.getLength(e.trim(t), n);
                return this.optional(n) || a >= i[0] && a <= i[1]
            }, min: function (e, t, n) {
                return this.optional(t) || e >= n
            }, max: function (e, t, n) {
                return this.optional(t) || n >= e
            }, range: function (e, t, n) {
                return this.optional(t) || e >= n[0] && e <= n[1]
            }, email: function (e, t) {
                return this.optional(t) || /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i.test(e)
            }, url: function (e, t) {
                return this.optional(t) || /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(e)
            }, date: function (e, t) {
                return this.optional(t) || !/Invalid|NaN/.test(new Date(e))
            }, dateISO: function (e, t) {
                return this.optional(t) || /^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(e)
            }, number: function (e, t) {
                return this.optional(t) || /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(e)
            }, digits: function (e, t) {
                return this.optional(t) || /^\d+$/.test(e)
            }, creditcard: function (e, t) {
                if (this.optional(t))return "dependency-mismatch";
                if (/[^0-9-]+/.test(e))return !1;
                var n = 0, i = 0, a = !1;
                e = e.replace(/\D/g, "");
                for (var s = e.length - 1; s >= 0; s--) {
                    var r = e.charAt(s), i = parseInt(r, 10);
                    a && (i *= 2) > 9 && (i -= 9), n += i, a = !a
                }
                return n % 10 == 0
            }, accept: function (e, t, n) {
                return n = "string" == typeof n ? n.replace(/,/g, "|") : "png|jpe?g|gif", this.optional(t) || e.match(new RegExp(".(" + n + ")$", "i"))
            }, equalTo: function (t, n, i) {
                var a = e(i).unbind(".validate-equalTo").bind("blur.validate-equalTo", function () {
                    e(n).valid()
                });
                return t == a.val()
            }
        }
    }), e.format = e.validator.format
}(jQuery), function (e) {
    var t = e.ajax, n = {};
    e.ajax = function (i) {
        i = e.extend(i, e.extend({}, e.ajaxSettings, i));
        var a = i.port;
        return "abort" == i.mode ? (n[a] && n[a].abort(), n[a] = t.apply(this, arguments)) : t.apply(this, arguments)
    }
}(jQuery), function (e) {
    jQuery.event.special.focusin || jQuery.event.special.focusout || !document.addEventListener || e.each({
        focus: "focusin",
        blur: "focusout"
    }, function (t, n) {
        function i(t) {
            return t = e.event.fix(t), t.type = n, e.event.handle.call(this, t)
        }

        e.event.special[n] = {
            setup: function () {
                this.addEventListener(t, i, !0)
            }, teardown: function () {
                this.removeEventListener(t, i, !0)
            }, handler: function (t) {
                return arguments[0] = e.event.fix(t), arguments[0].type = n, e.event.handle.apply(this, arguments)
            }
        }
    }), e.extend(e.fn, {
        validateDelegate: function (t, n, i) {
            return this.bind(n, function (n) {
                var a = e(n.target);
                return a.is(t) ? i.apply(a, arguments) : void 0
            })
        }
    })
}(jQuery), $(document).ready(function () {
    doValidation()
});