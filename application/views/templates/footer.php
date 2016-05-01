            </div>
        </div>
        <script src="<?php echo $resources;?>/jquery-ui/external/jquery/jquery.js" type="text/javascript"></script>
        <script src="<?php echo $resources;?>/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
        <script src="<?php echo $resources;?>/jquery-mobile/jquery.mobile-1.4.5.min.js" type="text/javascript"></script>
        <?php echo $log;?>
        <em>&copy; 2016</em>
        
        <script>
            // Set the thresholds for the swipe gestures.
            $.event.special.swipe.scrollSupressionThreshold = (screen.availWidth) / 9;
            $.event.special.swipe.horizontalDistanceThreshold = (screen.availWidth) / 9;
            $.event.special.swipe.verticalDistanceThreshold = (screen.availHeight) / 9;
            
            // Update the layout of the navigation panel.
            $("#nav-panel").trigger("updatelayout");
            
            // Allow opening of the navigation panel by swiping.
            $("#header").on("swiperight", function(event) {
               $("#nav-button").click();
            });
        </script>
    </body>
</html>