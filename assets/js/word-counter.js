/**
 * Comment Limiter - Frontend Counter and Validation Engine
 */
jQuery(document).ready(function($) {
    
    // ==========================================
    // 1. INITIALIZATION & CONFIGURATION
    // ==========================================
    var commentField = $("#comment");
    var commentForm  = $("#commentform"); 
    
    // Fail safely and exit early if the comment field isn't present on this page
    if (!commentField.length) {
        return;
    }

    // Parse backend configuration variables safely into base-10 integers
    var minChars = parseInt(cl_vars.minChars, 10);
    var maxChars = parseInt(cl_vars.maxChars, 10);

    // Dynamic UI Elements we will inject
    var counter, progressBarContainer, progressBar;


    // ==========================================
    // 2. HELPER FUNCTIONS (Reusable Code Blocks)
    // ==========================================

    /**
     * Replaces curly-brace placeholders with real live character data
     * @param {string} templateString - The raw message string containing placeholders
     * @param {number} currentLength - The active character count typed by user
     */
    function parsePlaceholders(templateString, currentLength) {
        return templateString
            .replace(/{min}/g, minChars)
            .replace(/{max}/g, maxChars)
            .replace(/{current}/g, currentLength);
    }

    /**
     * Sets up and injects the counter and progress bar HTML wrappers into the DOM
     */
    function buildDOMComponents() {
        // Build the text counter placeholder
        counter = $("<div>", {
            id: "char-counter", 
            class: "comment-limiter-counter",
            text: cl_vars.counterFormat.replace('%1$d', 0).replace('%2$d', maxChars)
        });

        // Build the horizontal progress bar wrappers
        progressBarContainer = $("<div>", { class: "comment-limiter-progress" });
        progressBar = $("<div>", { id: "char-progress-bar", class: "comment-limiter-progress-bar" });

        // Safely map and append elements inside the comment box layout parent wrapper
        var targetParent = commentField.parent();
        targetParent.append(counter); 
        progressBarContainer.append(progressBar); 
        targetParent.append(progressBarContainer); 
    }


    // ==========================================
    // 3. CORE LOGIC ENGINE RUNNERS
    // ==========================================

    // Kick off the script by building the HTML components on the screen
    buildDOMComponents();

    /**
     * Intercepts keystrokes/inputs and shifts visual presentation rules dynamically
     */
    commentField.on("input", function() {
        var charCount = commentField.val().length; 
        
        // Update live total text
        counter.text(cl_vars.counterFormat.replace('%1$d', charCount).replace('%2$d', maxChars));
        
        // Compute mathematical percentage width restrictions
        var progressPercent = Math.min((charCount / maxChars) * 100, 100);
        progressBar.css("width", progressPercent + "%"); 

        // Toggle validity CSS styling states instantly
        if (charCount < minChars || charCount > maxChars) {
            progressBar.removeClass("is-valid").addClass("is-invalid");
        } else {
            progressBar.removeClass("is-invalid").addClass("is-valid");
        }
    });

    /**
     * Form validation handler triggered during final submission cycle
     */
    commentForm.on("submit", function(event) {
        var charCount = commentField.val().length; 
        
        // Clean house by wiping any active visual error elements on the screen
        $(".comment-limiter-error").remove(); 

        var rawMessageTemplate = "";

        // Choose which warning configuration to display
        if (charCount < minChars) {
            rawMessageTemplate = cl_vars.minMessage; 
        } else if (charCount > maxChars) {
            rawMessageTemplate = cl_vars.maxMessage; 
        }

        // Action block: Fire only if an exception rule is broken
        if (rawMessageTemplate !== "") {
            event.preventDefault(); // Halt standard browser form submission

            // Process dynamic string parsing using our custom helper function
            var cleanedErrorMessage = parsePlaceholders(rawMessageTemplate, charCount);

            // Construct our temporary hidden error block element
            var errorDiv = $("<div>", {
                class: "comment-limiter-error",
                text: cleanedErrorMessage,
                style: "display: none;" 
            });

            // Smoothly animate UI swap
            counter.fadeOut(200, function() {
                commentField.parent().append(errorDiv);
                errorDiv.fadeIn(200); 
            });
            
            // Revert layout back to default counter framework after 5 seconds
            setTimeout(function() {
                errorDiv.fadeOut(200, function() {
                    $(this).remove(); 
                    counter.fadeIn(200); 
                    
                    // Recalculate the real level instead of dropping it to 0%
                    var currentCount = commentField.val().length;
                    var progressPercent = Math.min((currentCount / maxChars) * 100, 100);
                    progressBar.css("width", progressPercent + "%");
                });
            }, 5000);
        }
    });

});