$(function () {
    var a = $("#hideOrderID").val();
    var i = $("#txtUserName");
    var p = $("#txtMobile");
    var k = $("#selAreaA");
    var n = $("#selAreaB");
    var o = $("#selAreaC");
    var s = $("#selAreaD");
    var j = $("#txtAddr");
    var m = $("#txtZip");
    var q = $("#spDefult");
    var l = "为方便配送,请填写真实姓名";
    var d = "联系电话";
    var g = "请选择所在省份";
    var f = "请选择所在城市";
    var c = "请选择所在地区";
    var b = "请选择所在街道或乡镇";
    var t = "详细地址必须为3-100字之间,不包含特殊字符";
    var e = "邮政编码";
    var r = false;
    var h = function () {
        var z = function (J, I, H, G) {
            var F = function (K) {
                if (K.code == 0) {
                    I.empty();
                    var O = K.data;
                    var N = g;
                    if (G == "B") {
                        N = f
                    } else {
                        if (G == "C") {
                            N = c
                        } else {
                            if (G == "D") {
                                N = b
                            }
                        }
                    }
                    var M;
                    I.append($('<option value="-1" zip="">' + N + "</option>"));

                    for (var L = 0; L < O.length; L++) {
                        M = $('<option value="' + O[L].areaID + '" zip="' + O[L].areaZip + '">' + O[L].areaName + "</option>");
                        if (O[L].areaID == H) {
                            M.attr("selected", true)
                        }
                        I.append(M)
                    }
                }
                if (G == "D") {
                    if (I.find("option").length <= 1) {
                        I.parent().parent().parent().hide()
                    } else {
                        I.parent().parent().parent().show()
                    }
                }
            };
            if (J > -1) {
                $.getJSON(roots+"/user"+"/getChildArea", "areaID=" + J, F)
            }
        };
        var D = function (G) {
            var F = /(^[0-9]{7,8}$)|(^[0-9]{3,4}\-[0-9]{7,8}$)|(^[0-9]{7,8}$)|(^\([0-9]{3,4}\)[0-9]{3,8}$)|(^0{0,1}1[0-9]{10}$)/;
            return F.test(G)
        };
        var B = function (G) {
            var F = /^\d{6}$/;
            return F.test(G)
        };
        var u = function (G) {
            var F = /^[a-zA-Z0-9_\u4e00-\u9fa5]+$/;
            return F.test(G)
        };
        var A = function (G) {
            if (G.length < 2 || G.length > 5) {
                return false
            }
            var F = /^[\u4E00-\u9FA5]+$/;
            return F.test(G)
        };
        var w = function (F) {
            var G = ["老", "大", "小", "阿", "啊"];
            var H = ["先生", "小姐", "女士"];
            if ($.inArray(F.substring(0, 1), G) > -1 || F.indexOf(H[0]) > -1 || F.indexOf(H[1]) > -1 || F.indexOf(H[2]) > -1) {
                return false
            }
            return true
        };
        var x = function (G) {
            if (G.length < 3 || G.length > 100) {
                return false
            }
            var F = /^[a-zA-Z0-9#_\(\)\-\s\u4e00-\u9fa5]{3,100}$/;
            return F.test(G)
        };
        var v = function (J, I, G, F, H) {
            $.PageDialog.fail(J, null, G, F, H);
            r = false
        };
        var y = function (G, F) {
            $.PageDialog.ok(G, F)
        };
        function C(G, F) {
            $.PageDialog.confirm(G, F)
        }
        var E = function (J) {
            var G = -1;
            var L = -1;
            var H = -1;
            var F = -1;
            var K = function (M) {
                M.focus(function () {
                    M.css("color", "#666666")
                }).blur(function () {
                    if (M.val() == "") {
                        M.val(defVal).css("color", "#bbbbbb")
                    }
                })
            };
            K(i);
            K(p);
            K(j);

            k.change(function (){
                var M = k.val();
                if (M == -1) {
                    k.css("color", "#bbbbbb");
                    n.html('<option value="-1">' + f + "</option>").css("color", "#bbbbbb");
                    o.html('<option value="-1">' + c + "</option>").css("color", "#bbbbbb");
                    s.html('<option value="-1">' + b + "</option>").css("color", "#bbbbbb")
                } else {
                    k.css("color", "#666666");
                    n.html('<option value="-1">' + f + "</option>");
                    o.html('<option value="-1">' + c + "</option>");
                    s.html('<option value="-1">' + b + "</option>");
                    m.val("");
                    z(M, n, 0, "B")
                }
            });
            n.change(function () {
                var M = n.val();
                if (M == -1) {
                    n.css("color", "#bbbbbb");
                    o.html('<option value="-1">' + c + "</option>").css("color", "#bbbbbb");
                    s.html('<option value="-1">' + b + "</option>").css("color", "#bbbbbb")
                } else {
                    n.css("color", "#666666");
                    o.html('<option value="-1">' + c + "</option>");
                    s.html('<option value="-1">' + b + "</option>");
                    z(M, o, 0, "C")
                }
            });
            o.change(function () {
                var N = o.val();
                if (N == -1) {
                    o.css("color", "#bbbbbb");
                    s.html('<option value="-1">' + b + "</option>").css("color", "#bbbbbb")
                } else {
                    o.css("color", "#666666");
                    s.html('<option value="-1">' + b + "</option>");
                    var M = o.find("option:selected").attr("zip");
                    m.val(M);
                    z(N, s, 0, "D")
                }
            });
            s.change(function () {
                var M = s.val();
                if (M == -1) {
                    s.css("color", "#bbbbbb")
                } else {
                    s.css("color", "#666666")
                }
            });
            if (G == -1) {
                z(1, k, 0, "A");
                k.css("color", "#bbbbbb");
                n.html('<option value="-1">' + f + "</option>").css("color", "#bbbbbb");
                o.html('<option value="-1">' + c + "</option>").css("color", "#bbbbbb");
                s.html('<option value="-1">' + b + "</option>").css("color", "#bbbbbb");
                m.css("color", "#bbbbbb")
            } else {
                k.css("color", "#666666");
                n.css("color", "#666666");
                o.css("color", "#666666");
                m.css("color", "#666666");
                z(1, k, G, "A");
                z(G, n, L, "B");
                z(L, o, H, "C");
                z(H, s, F, "D")
            }
            var I = false;
            q.click(function () {
                q.children("span").attr("class", I ? "z-pay-ment" : "z-pay-mentsel");
                I = !I
            });
            $("#btnCancel").click(function () {
                history.go(-1)
            });
            $("#btnSure").click(function () {
                if (r) {
                    return
                }
                r = true;
                var N = $(window).width();
                var Q = (N) / 2 - i.offset().left - 106;
                var T = i.val().trim();
                if (T == "" || T == l) {
                    v("请填写收货人姓名", i, 15, Q,
                            function () {
                                i.focus()
                            });
                    return false
                } else {
                    if (!A(T)) {
                        v("收货人为2-5位中文", i, 15, Q,
                                function () {
                                    i.focus()
                                });
                        return false
                    } else {
                        if (!w(T)) {
                            v("请填写真实收货人姓名", i, 15, Q,
                                    function () {
                                        i.focus()
                                    });
                            return false
                        }
                    }
                }
                var U = p.val().trim();
                if (U == d) {
                    v("请填写联系电话", p, 15, Q,
                            function () {
                                p.focus()
                            });
                    return false
                }
                if (!D(U)) {
                    v("联系电话格式不正确", p, 15, Q,
                            function () {
                                p.focus()
                            });
                    return false
                }
                var M = k.val();
                if (M == -1) {
                    v("请选择省份", k, 15, Q);
                    return false
                }
                var S = n.val();
                if (S == -1) {
                    v("请选择城市", n, 15, Q);
                    return false
                }
                var P = o.val();
                if (P == -1) {
                    v("请选择地区", o, 15, Q);
                    return false
                }
                var W = s.val();
                if (s.find("option").length > 1) {
                    if (W == -1) {
                        v("请选择街道或乡镇", s, 15, Q);
                        return false
                    }
                }
                var V = j.val().trim();
                if (V == "" || V == t) {
                    v("请填写详细地址", j, 15, Q,
                            function () {
                                j.focus()
                            });
                    return false
                } else {
                    if (!x(V)) {
                        v("详细地址不合法", j, 15, Q,
                                function () {
                                    j.focus()
                                });
                        return false
                    }
                }
                var R = m.val().trim();
                if (R != "" && R != e) {
                    if (!B(R)) {
                        v("邮编格式不正确", m, 15, Q);
                        return false
                    }
                }
                var O = k.find("option:selected").text() + " " + n.find("option:selected").text() + " " + o.find("option:selected").text();
                //alert(O);
                if (W > 0) {
                    O = O + " " + s.find("option:selected").text()
                }
                $.post(roots + "/user/editUserContact", {
                    Name: T,
                    Phone: U,
                    OrderAddress: O,
                    Address: V,
                    Zip: R,
                    IsDefault: I ? 1 : 0,
                    AddrID: 0,
                    OID: a,
                    CID: P,
                    DID: W
                },
                        function (X) {
                            //alert(X.code);
                            if (X.code == 0) {
                                y("添加成功",
                                        function () {
                                            location.replace(roots + "/user/orderdetail/id/" + a)
                                        })
                            } else {
                                if (X.code == 2) {
                                    v("参数错误，请检查")
                                } else {
                                    if (X.code == 3) {
                                        v("收货人格式错误")
                                    } else {
                                        v("添加失败，请重试")
                                    }
                                }
                            }
                        },
                        "json")
            })
        };
        E()
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=151104", h)
});