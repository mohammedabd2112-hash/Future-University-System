const loginForm = document.getElementById("loginForm");

if (loginForm) {

    loginForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const message = document.getElementById("message");
        const loginButton = document.getElementById("loginButton");

        if (email === "" || password === "") {

            message.textContent =
                "يرجى إدخال البريد الإلكتروني وكلمة المرور";

            return;
        }

        loginButton.disabled = true;
        loginButton.textContent = "جاري تسجيل الدخول...";

        try {

            const response = await fetch("../api/login.php", {

                method: "POST",

                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body: new URLSearchParams({
                    email: email,
                    password: password
                })

            });

            const data = await response.json();

            if (data.success) {

                message.textContent =
                    "تم تسجيل الدخول بنجاح";

                console.log(
                    "بيانات المستخدم:",
                    data.user
                );

                /*
                 * تحديد الصفحة حسب نوع المستخدم
                 */

                if (data.user.role === "admin") {

                    window.location.href =
                        "../admin/index.php";

                } else if (data.user.role === "student") {

                    window.location.href =
                        "students.php";

                } else {

                    message.textContent =
                        "نوع المستخدم غير معروف";

                    loginButton.disabled = false;

                    loginButton.textContent =
                        "تسجيل الدخول";
                }

            } else {

                message.textContent =
                    data.message;

                loginButton.disabled = false;

                loginButton.textContent =
                    "تسجيل الدخول";
            }

        } catch (error) {

            console.error(error);

            message.textContent =
                "حدث خطأ أثناء الاتصال بالخادم";

            loginButton.disabled = false;

            loginButton.textContent =
                "تسجيل الدخول";
        }

    });

}