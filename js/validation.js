const validation = new JustValidate("#signup");

validation
    .addField("#firstname", [
        {
            rule: "required"
        }
    ])
    .addField("#lastname", [
        {
            rule: "required"
        }
    ])
    .addField("#username", [
        {
            rule: "required"
        }
    ])
    .addField("#email", [
        {
            rule: "required"
        },
        {
            rule: "email"
        // },
        // {
        //     validator: (value) => () => {
        //         return fetch("validate-email.php?email=" + encodeURIComponent(value))
        //                .then(function(response) {
        //                    return response.json();
        //                })
        //                .then(function(json) {
        //                    return json.available;
        //                });
        //     },
        //     errorMessage: "email already taken"
        }
    ])
    .addField("#phonenumber", [
        {
            rule: "required"
        }
    ])
    .addField("#password", [
        {
            rule: "required"
        },
        {
            rule: "password"
        }
    ])
    .addField("#password_confirmation", [
        {
            validator: (value, fields) => {
                return value === fields["#password"].elem.value;
            },
            errorMessage: "Passwords should match"
        }
    ])
    .onSuccess((event) => {
        event.preventDefault(); // Prevents default form submission to confirm data
        document.getElementById("signup").submit();
    });
    
    
    
    
    
    
    
    
    
    
    
    
    
