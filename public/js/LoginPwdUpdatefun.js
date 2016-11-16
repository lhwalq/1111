$(function() {
    var a = function() {
        var e = {
            Empty: "当前密码不能为空",
            LenErr: "密码格式不正确，请检查",
            Err: "原始密码错误,请重新输入"
        };
        var i = {
            Empty: "新密码不能为空",
            LenErr: "新密码格式不正确，请检查"
        };
        var g = {
            Empty: "确认新密码不能为空",
            Err: "两次密码输入不一致,请重新输入"
        };
        var d = function(m) {
            var l = function(p, q, n) {
                var o = new RegExp(q, "g");
                return p.replace(o, n)
            };
            var m = escape(m);
            m = l(m, "\\+", "%2B");
            m = l(m, "/", "%2F");
            return m
        };
        var k = function(m) {
            var l = /^[\S]{6,20}$/;
            if (!l.exec(m)) {
                return false
            }
            return true
        };
        var j = function(m) {
            var l = /^(?![A-z]+$)(?!\d+$)(?![\W_]+$)^.{8,20}$/;
            if (!l.exec(m)) {
                return false
            }
            return true
        };
        var b = function(l) {
            $.PageDialog.fail(l)
        };
        var f = function(m, l) {
            $.PageDialog.ok(m, l)
        };
        var c = function(l, m) {
            if (!isLoaded) {
                return
            }
            isLoaded = false;
            $.getJSON(roots+"/user/updateUserPwd?userOldPwd=" + l+"&userNewPwd=" + m,
            function(n) {
					
                if (n.code == 0) {
                    f("修改成功！",
                    function() {
                        location.href = roots+"/user/home"
                    });
                    return
                } else {
                    if (n.code == 1) {
                        b(e.Err);
                        $("#txtOldPwd").focus();
                        $("#txtOldPwd").select()
                    } else {
                        if (n.code == 2) {
                            b("登录密码不能和支付密码相同！");
                            $("#txtNewPwd").focus();
                            $("#txtNewPwd").select()
                        } else {
                            f("修改失败，请重试！")
                        }
                    }
                }
                isLoaded = true
            },
            "json")
        };
        var h = function() {
            if (!isLoaded) {
                return
            }
            var m = $("#txtOldPwd").val();
            if (m == "") {
                b(e.Empty);
                return false
            } else {
                if (!k(m)) {
                    b(e.LenErr);
                    return false
                }
            }
            var n = $("#txtNewPwd").val();
            if (n == "") {
                b(i.Empty);
                return false
            } else {
                if (!j(n)) {
                    b(i.LenErr);
                    return false
                }
            }
            var l = $("#txtConNewPwd").val();
            if (l == "") {
                b(g.Empty);
                return false
            } else {
                if (l != n) {
                    b(g.Err);
                    return false
                }
            }
            c(m, n)
        };
        $("#btnSubmit").bind("click",
        function() {
            h()
        });
        $("input[type='password']").bind("keydown",
        function(m) {
            var l = (window.event) ? event.keyCode: m.keyCode;
            if (l == 32) {
                return false
            } else {
                if (l == 13) {
                    return false
                }
            }
            return true
        });
        isLoaded = true
    };
    Base.getScript(pub+"/JS/pageDialog.js?v=130826", a)
});