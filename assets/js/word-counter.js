// Ensure the script runs only after the HTML document is fully loaded
jQuery(document).ready(function($) {
    
<<<<<<< HEAD
    // Select the comment input field, the form, and the submit button
    var commentField = $("#comment");
    var commentForm  = $("#commentform"); // Triggers on click OR enter key press
    
    // Retrieve minimum and maximum character limits from localized variables
    var minChars = parseInt(cl_vars.minChars, 10);
    var maxChars = parseInt(cl_vars.maxChars, 10);
=======
    // Select the comment input field and the submit button by their IDs
    var commentField = $("#comment");
    var submitButton = $("#submit");
    
    // Retrieve minimum and maximum character limits from localized variables (assumed from WordPress backend)
    var minChars = cl_vars.minChars;
    var maxChars = cl_vars.maxChars;
>>>>>>> 797581573fa8bbb6089f60c9a0d128e3f4c09ce0

    // Check if the comment field exists on the page
    if (commentField.length) {

        // Create a new <div> element to display the character counter
        var counter = $("<div>", {
<<<<<<< HEAD
            id: "char-counter", 
=======
            id: "char-counter", // Set the ID for styling and reference
>>>>>>> 797581573fa8bbb6089f60c9a0d128e3f4c09ce0
            class: "comment-limiter-counter",
            text: cl_vars.counterFormat.replace('%1$d', 0).replace('%2$d', maxChars)
        });

<<<<<<< HEAD
        // Create the progress bar container
=======
        // Create the progress bar container with a fixed height and background color
>>>>>>> 797581573fa8bbb6089f60c9a0d128e3f4c09ce0
        var progressBarContainer = $("<div>", {
            class: "comment-limiter-progress"
        });
        
<<<<<<< HEAD
        // Create the progress bar inside the container
        var progressBar = $("<div>", {
            id: "char-progress-bar", 
            class: "comment-limiter-progress-bar"
        });

        // Append components safely below the comment field
        commentField.parent().append(counter); 
        progressBarContainer.append(progressBar); 
        commentField.parent().append(progressBarContainer); 

        // Event listener for user input in the comment field
        commentField.on("input", function() {
            var charCount = commentField.val().length; 
            counter.text(cl_vars.counterFormat.replace('%1$d', charCount).replace('%2$d', maxChars));
            
            // Calculate progress percentage
            var progressPercent = Math.min((charCount / maxChars) * 100, 100);
            progressBar.css("width", progressPercent + "%"); 

            // Adjust the progress bar classes based on character count
            if (charCount < minChars || charCount > maxChars) {
                progressBar.removeClass("is-valid").addClass("is-invalid");
            } else {
                progressBar.removeClass("is-invalid").addClass("is-valid");
            }
        });

        // Event listener hooked to FORM SUBMIT instead of button click
        commentForm.on("submit", function(event) {
            var charCount = commentField.val().length; 
            
            // Instantly remove previous errors to prevent clutter
            $(".comment-limiter-error").remove(); 

            var errorMsgText = "";

            // Evaluate if comment meets requirements
            if (charCount < minChars) {
                errorMsgText = cl_vars.minRequired.replace('%d', minChars);
            } else if (charCount > maxChars) {
                errorMsgText = cl_vars.maxAllowed.replace('%d', maxChars);
            }

            // If there's an error string, halt submission and display it
            if (errorMsgText !== "") {
                event.preventDefault(); // Stop the form from submitting to the backend

                // 1. Build the error element (initially hidden using inline CSS styles)
                var errorDiv = $("<div>", {
                    class: "comment-limiter-error",
                    text: errorMsgText,
                    style: "display: none;" 
                });

                // 2. Hide counter first, then append and properly chain the fadeIn animation
                counter.fadeOut(200, function() {
                    commentField.parent().append(errorDiv);
                    errorDiv.fadeIn(200); 
                });
                
                // 3. Clear error and restore counter after 3 seconds
                setTimeout(function() {
                    errorDiv.fadeOut(200, function() {
                        $(this).remove(); 
                        counter.fadeIn(200); 
=======
        // Create the progress bar inside the container, initially empty
        var progressBar = $("<div>", {
            id: "char-progress-bar", // ID for reference and styling
            class: "comment-limiter-progress-bar"
        });

        // Append the counter and the progress bar container (with bar inside) to the page, right below the comment field
        commentField.parent().append(counter); // Adds the counter
        progressBarContainer.append(progressBar); // Adds the progress bar to its container
        commentField.parent().append(progressBarContainer); // Adds the container with the progress bar

        // Event listener for user input in the comment field
        commentField.on("input", function() {
            var charCount = commentField.val().length; // Calculate current character count
            counter.text(cl_vars.counterFormat.replace('%1$d', charCount).replace('%2$d', maxChars));
            
            // Calculate the progress percentage based on maxChars, capped at 100%
            var progressPercent = Math.min((charCount / maxChars) * 100, 100);
            progressBar.css("width", progressPercent + "%"); // Set the width of the progress bar

            // Adjust the progress bar color based on character count:
            if (charCount < minChars) {
                progressBar.removeClass("is-valid").addClass("is-invalid");
            } else if (charCount >= minChars && charCount <= maxChars) {
                progressBar.removeClass("is-invalid").addClass("is-valid");
            } else {
                progressBar.removeClass("is-valid").addClass("is-invalid");
            }
        });

        // Event listener for the submit button click
        submitButton.on("click", function(event) {
            var charCount = commentField.val().length; // Get the current character count
            $(".comment-limiter-error").remove(); // Remove any previous error message to prevent duplicates

            // Check if the character count is below the minimum
            if (charCount < minChars) {
                event.preventDefault(); // Prevent form submission
                
                // Create an error message for minimum character requirement
                var errorMin = $("<div>", {
                    id: "error-msg1",
                    class: "comment-limiter-error",
                    text: cl_vars.minRequired.replace('%d', minChars),
                });

                // Hide the counter, show error message, then revert after 3 seconds
                counter.fadeOut(200, function() {
                    commentField.parent().append(errorMin.fadeIn(200)); // Append and fade in error
                });
                
                // After 3 seconds, fade out and remove error message, fade in counter again
                setTimeout(function() {
                    errorMin.fadeOut(200, function() {
                        $(this).remove(); // Remove error from DOM
                        counter.fadeIn(200); // Show the counter again
                    });
                }, 3000);

            // Check if the character count exceeds the maximum
            } else if (charCount > maxChars) {
                event.preventDefault(); // Prevent form submission
                
                // Create an error message for maximum character limit
                var errorMax = $("<div>", {
                    id: "error-msg2",
                    class: "comment-limiter-error",
                    text: cl_vars.maxAllowed.replace('%d', maxChars),
                });

                // Hide the counter, show error message, then revert after 3 seconds
                counter.fadeOut(200, function() {
                    commentField.parent().append(errorMax.fadeIn(200)); // Append and fade in error
                });

                // After 3 seconds, fade out and remove error message, fade in counter again
                setTimeout(function() {
                    errorMax.fadeOut(200, function() {
                        $(this).remove(); // Remove error from DOM
                        counter.fadeIn(200); // Show the counter again
>>>>>>> 797581573fa8bbb6089f60c9a0d128e3f4c09ce0
                    });
                }, 3000);
            }
        });
    }
<<<<<<< HEAD
});
=======
});
>>>>>>> 797581573fa8bbb6089f60c9a0d128e3f4c09ce0
