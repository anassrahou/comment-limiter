// Ensure the script runs only after the HTML document is fully loaded
jQuery(document).ready(function($) {
    
    // Select the comment input field, the form, and the submit button
    var commentField = $("#comment");
    var commentForm  = $("#commentform"); // Triggers on click OR enter key press
    
    // Retrieve minimum and maximum character limits from localized variables
    var minChars = parseInt(cl_vars.minChars, 10);
    var maxChars = parseInt(cl_vars.maxChars, 10);

    // Check if the comment field exists on the page
    if (commentField.length) {

        // Create a new <div> element to display the character counter
        var counter = $("<div>", {
            id: "char-counter", 
            class: "comment-limiter-counter",
            text: cl_vars.counterFormat.replace('%1$d', 0).replace('%2$d', maxChars)
        });

        // Create the progress bar container
        var progressBarContainer = $("<div>", {
            class: "comment-limiter-progress"
        });
        
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
                    });
                }, 3000);
            }
        });
    }
});