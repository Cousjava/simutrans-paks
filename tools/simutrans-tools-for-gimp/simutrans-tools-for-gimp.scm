;; SIMUTRANS TOOLS FOR GIMP 0.6+ working copy
;; ============================================================================================
;;
;; working copy
;; ADD Multilayer transformtion tool (rotate & flip)
;; CHG Multilayer tools can filter for visible/not visible
;; CHG Swap Colors default to fore/back colors
;; CHG Isometric Surface Helper tool converts any to any: front, wall, floor, slope with 
;;     source autodetect and optional flip/rotate transformation
;; CHG Isometric surface tools now crop the result
;;
;; ver. 0.6 - 19/06/2012
;; CHG Menu reordering
;; ADD New Isometric surface tool
;; CHG Set grid also for Pak160 and (optional) resizes image sized layers
;; CHG Swap colors can also use active front/back colors
;; ADD Copy/Cut and Paste multilayer tools
;; ADD Move multilayer tool
;;
;; ver. 0.5 - 15/03/2012
;; ADD Lookup (convert) operation in Special Color Helper
;; ADD Lighten/Darken operation in Special Color Helper
;; ADD Special Color Helper sample merged select
;; ADD Set grid tool (optional resize)
;; ADD Swap colors tool
;; ADD internal stats, selection-save, selection-by-color, selection-fill 
;;	 and color-lookup functions
;; CHG Export flatten now optional (saves time for large images if not needed)
;; CHG code optimization
;;
;; ver. 0.4 - 13/03/2012
;; ADD PNG Export tool
;; FIX Remove and Repair only worked on first open image (image id=1)
;;
;; ver. 0.2 - 09/03/2012
;; FIX it painted all screen if the color was not found
;; CHG some optimization 
;; CHG progress bar now working
;;
;; ver. 0.1 - 08/03/2012
;; initial release
;;
;;
;; This program is free software; you can redistribute it and/or modify
;; it under the terms of the Simutrans Artistic License.
;; See http://forum.simutrans.com
;;
;; This program is distributed in the hope that it will be useful,
;; but WITHOUT ANY WARRANTY; without even the implied warranty of
;; MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
;;
;;

(define debug-mode #f)

;;
;; ============================================================================================
;; CONSTANTS AND LISTS

(define non-darkening-greys-list 
	'((107 107 107)  
	  (155 155 155) 
	  (179 179 179) 
	  (201 201 201) 
	  (223 223 223)))
(define window-colors-list 
	'(( 77  77  77)
	  ( 87 101 111)  
	  (193 177 209)))
(define primary-player-colors-list 
	'(( 36  75 103)  
	  ( 57  94 124) 
	  ( 76 113 145)
	  ( 96 132 167)
	  (116 151 189)
	  (136 171 211)
	  (156 190 233)
	  (176 210 255)))
(define secondary-player-colors-list 
	'((123  88   3)  
	  (142 111   4) 
	  (161 134   5)
	  (180 157   7)
	  (198 180   8)
	  (217 203  10)
	  (236 226  11)
	  (255 249  13)))
(define lights-list 
	'((127 155 241) 
	  (255 255  83) 
	  (255  33  29) 
	  (  1 221   1)
	  (227 227 255)
	  (255   1 127)
	  (  1   1 255)))
(define transparent-color-list 
	'((231 255 255)))
(define (SH-LIST . default)	
	(list	(if (null? default) "Default" (car default))
			"Floor tile" 
			"Wall" 
			"Half slope"
			"Double slope"
			"Front image" 
	)
)
(define (OR-LIST . default)	
	(list	(if (null? default) "Default" (car default))
			"South" 
			"East" 
			"North" 
			"West"
	)
)
(define (AL-LIST . default)	
	(list	(if (null? default) "Default" (car default))
			"Center" 
			"Right" 
			"Left" 
	)
)
(define  TR-LIST 
	'("None" "Rotate 90� clockwise" "Rotate 90� counter-clockwise" "Rotate 180�" "Flip Horizontally" "Flip Vertically"))
(define SH-DEFAULT		0)
(define SH-TILE			1)
(define SH-WALL			2)
(define SH-SLOPE-1		3)
(define SH-SLOPE-2		4)
(define SH-FRONT		5)
(define OR-DEFAULT		0)
(define OR-SOUTH		1)
(define OR-EAST			2)
(define OR-NORTH		3)
(define OR-WEST 		4)
(define AL-DEFAULT		0)
(define AL-CENTER		1)
(define AL-RIGHT		2)
(define AL-LEFT			3)
(define TR-NONE			0)
(define TR-ROTATE-90	1)
(define TR-ROTATE-270	2)
(define TR-ROTATE-180	3)
(define TR-FLIP-HOR		4)
(define TR-FLIP-VER		5)
(define TR-CUSTOM		6)
	
;;
;; ============================================================================================
;; PRIVATE

(define (debug . args)
	(when debug-mode 
		(gimp-message (join " " (map x->string args)))
		(display args)(newline)
    )
)

(define (x->string x)
    (cond
        ((null? x) "")
        ((string? x) x)
        ((number? x) (number->string x))
        ((symbol? x) (symbol->string x))
        ((boolean? x) (if x "#t" "#f"))
        ((vector? x) (string-append "#" (x->string (vector->list x))))
        ((list? x) (string-append "(" (join " " (map x->string x)) ")"))
        ((pair? x) (string-append "(" (x->string (car x)) " . " (x->string (cdr x)) ")"))
        (else "<unknown>")
    )
)

(define (join sep list)
    (if (pair? list)
        (string-append (x->string (car list)) (if (pair? (cdr list)) sep "") (join sep (cdr list)))
        (x->string list)
    )
)

(define (for-each-layer image filter procedure)
	(let* 
		(
			(layer-list		(vector->list (cadr (gimp-image-get-layers image))))
			(mask			(if (list? filter) (car filter) filter))
			(visible-only	(if (list? filter) (cadr filter) FALSE))
		)
		(for-each
			(lambda (this-layer)
				;Check layer name against mask, use Regular Expressions
				(when (re-match mask (car (gimp-drawable-get-name this-layer)))
					(when (or (zero? visible-only) (positive? (car (gimp-drawable-get-visible this-layer))))
						(apply procedure (list this-layer))
					)
				)
			) ;end of lambda (this-layer)
				layer-list
		) ;end of for-each
	)
)

(define (edit-buffer-in-temporary-image temp-buffer-name procedure)
	(let* 
		(
			(temp-image			0)
			(temp-layer			0)
		)

		;Paste as temp image
		(set! temp-image (car (gimp-edit-named-paste-as-new temp-buffer-name)))
		(set! temp-layer (car (vector->list (cadr (gimp-image-get-layers temp-image)))))				
		(gimp-image-undo-disable temp-image)
		(gimp-buffer-delete temp-buffer-name)

		;Do things
		(apply procedure (list temp-image temp-layer))
		
		;Check if temp-image was deleted by procedure
		(if (zero? (car (gimp-image-is-valid temp-image)))
		
			;Return nil
			nil
			
			(begin
				;Copy back temp 
				(gimp-selection-all temp-image)
				(set! temp-buffer-name (car (gimp-edit-named-copy-visible temp-image temp-buffer-name)))

				;Delete temp image	
				(gimp-image-delete temp-image)		
				
				;Return actual buffer name
				(list temp-buffer-name)
			)
		)
	)
)

(define (merge-buffers . buffers)
	(case (length buffers)
		((0) nil)
		((1) buffers)
		(else
			
			(let* 
				(
					(temp-image			0)
					(temp-layer			0)
					(result-buffer		"simutrans-merged-buffer")
				)

				;Paste as temp image
				(set! temp-image (car (gimp-edit-named-paste-as-new (car buffers))))
				(set! temp-layer (car (vector->list (cadr (gimp-image-get-layers temp-image)))))				
				(gimp-image-undo-disable temp-image)
				(gimp-buffer-delete (car buffers))
				
				(while (not (null? (cdr buffers)))
					(set! buffers (cdr buffers))
					(gimp-floating-sel-anchor 
						(car 
							(gimp-edit-named-paste temp-layer (car buffers) 1)
						)
					)
					(gimp-buffer-delete (car buffers))					
				)
				
				;Check if temp-image was deleted by procedure
				(if (zero? (car (gimp-image-is-valid temp-image)))
				
					;Return nil
					nil
					
					(begin
						;Copy back temp 
						(gimp-selection-all temp-image)
						(set! result-buffer (car (gimp-edit-named-copy-visible temp-image result-buffer)))

						;Delete temp image	
						(gimp-image-delete temp-image)		
						
						;Return actual buffer name
						(list result-buffer)
					)
				)
			)
			
		)
	)
)

(define (selection-save image) 
	(if (zero? (car (gimp-selection-is-empty image))) (car (gimp-selection-save image)) 0)
)
	
(define (selection-by-color drawable color threshold selection-mask)
	(gimp-by-color-select drawable color threshold CHANNEL-OP-REPLACE FALSE FALSE 0 FALSE)
	(unless (zero? selection-mask) 
		(gimp-selection-combine selection-mask CHANNEL-OP-INTERSECT)
	)
)

(define (selection-fill drawable color)
	(when (zero? (car (gimp-selection-is-empty (car (gimp-drawable-get-image drawable)))))
		(gimp-context-set-foreground color)
		(gimp-edit-fill drawable FOREGROUND-FILL)
	)
)
   
(define (color-lookup image color vertical)
	(let* 
		(
			(drawable (car (gimp-image-get-active-layer image)))
			(color1 '()) 
			(color2 '()) 
			(koord  '())
			(x1 0) (y1 0)
			(x2 0) (y2 0)
		)
		(gimp-by-color-select drawable color 0 CHANNEL-OP-REPLACE FALSE FALSE 0 FALSE)
		(set! koord (gimp-selection-bounds image))
		(gimp-selection-none image)
		(if (zero? (car koord))
			'()
			(begin
				(set! x1	(list-ref koord 1)   )
				(set! y1	(list-ref koord 2)   )
				(set! x2 (- (list-ref koord 3) 1))
				(set! y2 (- (list-ref koord 4) 1))
				(let loop (
					(x1 x1) (y1 y1) (x2 x2) (y2 y2)
				)
					(set! x1 (+ x1 (if (zero? vertical) 0 1))) 
					(set! y1 (+ y1 (if (zero? vertical) 1 0))) 
					(set! x2 (+ x2 (if (zero? vertical) 0 1))) 
					(set! y2 (+ y2 (if (zero? vertical) 1 0)))					 
					(set! color1 (car (gimp-image-pick-color image drawable x1 y1 FALSE FALSE 0)))
					(set! color2 (car (gimp-image-pick-color image drawable x2 y2 FALSE FALSE 0)))
					(cond
						((equal? color1 color2)
							color1)
						((or (>= y2 (car (gimp-image-height image))) (>= x2 (car (gimp-image-width image))))
							'())
						(else 
							(loop x1 y1 x2 y2))
					)
				)
			)
		)
	)
)   
   
(define (buffer-name drawable)
	(let* 
		(
			(prefix	   	"copy-of-layer")
			(separator		"-")
		)
		(string-append 
			prefix 
			separator 
			(if (< drawable 0)
				""
				(string-append
					;(number->string drawable) 
					;separator 
					(car (gimp-drawable-get-name drawable))
				)
			)	
		)
	)
)	

(define (stats m l)
	(cons (/ (length (cdr (member m (reverse l)))) (length l)) (/ (length l)))
)

(define (crop-to-alpha image drawable)
	(gimp-image-undo-group-start image)
	(gimp-selection-layer-alpha drawable)
	(let* ((result (gimp-selection-bounds image)))
		(gimp-image-crop image 
			(- (list-ref result 3) (list-ref result 1)) ;new-width 
			(- (list-ref result 4) (list-ref result 2)) ;new-height 
			(list-ref result 1) ;offx 
			(list-ref result 2) ;offy
		)
	)
	(gimp-image-undo-group-end image)
)

(define (translate image drawable offx offy clone)
	(gimp-image-undo-group-start image)
	(when (zero? (car (gimp-selection-is-empty image)))
		(if (zero? clone)
			(gimp-floating-sel-anchor (car (gimp-selection-float drawable offx offy)))
			(let* ((buffer (car (gimp-edit-named-copy drawable "simutrans-clone-selection"))))
				(gimp-selection-translate image offx offy)
				(gimp-floating-sel-anchor (car (gimp-edit-named-paste drawable buffer 1)))
				(gimp-buffer-delete buffer)
			)	
		)
	)
	(gimp-image-undo-group-end image)
)

(define (scale-image image drawable . param)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(width 	 		(car (gimp-drawable-width 	drawable)))
			(height 		(car (gimp-drawable-height 	drawable)))
			(width-ratio	(if (>= (length param) 1) (list-ref param 0) 1))
			(height-ratio	(if (>= (length param) 2) (list-ref param 1) width-ratio))
			(interpolation	(if (>= (length param) 3) (list-ref param 2) INTERPOLATION-NONE))
			(new-width		(ceiling (* width (cond ((positive? width-ratio) width-ratio) ((negative? width-ratio) (/ (abs width-ratio))) (else 1)))))
			(new-height		(ceiling (* height (cond ((positive? height-ratio) height-ratio) ((negative? height-ratio) (/ (abs height-ratio))) (else 1)))))
		)
		(gimp-image-scale-full image new-width new-height interpolation)
	)
	(gimp-image-undo-group-end image)
)

(define (horizontal-skew image drawable . param)
	(gimp-image-undo-group-start image)
	(if (null? param) (set! param (list 0)))
	(let*
		(
			;(progress	(if (list? (car (last param))) (car (last param)) nil))
			;(direction	(if (null? progress) param (reverse (cdr (reverse param)))))
			(direction	param)
			(width 	 	(car (gimp-drawable-width 	drawable)))
			(height 	(car (gimp-drawable-height 	drawable)))
			(new-width  (ceiling (+ width (abs (* (/ (apply + direction) (length direction)) height)))))
			(offx		(if (positive? (apply + direction)) 0 (- new-width width)))
			(index		1)
		)
		(gimp-image-resize image new-width height offx 0)
		(gimp-layer-resize-to-image-size drawable)
		(while (< index height)
			;(unless (null? progress)
			;	(gimp-progress-set-text (list-ref progress 2))
			;	(gimp-progress-update (+ (list-ref progress 0) (* (/ index height) (- (list-ref progress 1) (list-ref progress 0)))))
			;)
			(gimp-progress-pulse)
			(let* ((dir	(list-ref direction (modulo (- index 1) (length direction)))))
				(unless (zero? dir)
					(gimp-rect-select image 0 0 new-width (- height index) CHANNEL-OP-REPLACE 0 0)
					(gimp-floating-sel-anchor (car (gimp-selection-float drawable dir 0)))
				)
			)
			(set! index (+ index 1))
		)
	)
	(gimp-image-undo-group-end image)
)

(define (vertical-skew image drawable . param)
	(gimp-image-undo-group-start image)
	(let*
		(
			(direction	(if (null? param) +1 (if (<= (car param) 0) -1 +1)))
			(alignment	(if (or (null? param) (null? (cdr param))) AL-LEFT (cadr param)))
			;(progress	(if (or (null? param) (null? (cdr param)) (null? (cddr param))) nil (caddr param)))
			(width 	 	(car (gimp-drawable-width 	drawable)))
			(height 	(car (gimp-drawable-height 	drawable)))
			(new-width  (* (ceiling (/ width 4)) 4))			
			(new-height (* (+ height (if (odd? height) 1 0)) 1.5))
			(offx		(cond	((= alignment AL-RIGHT) (- new-width width))
								((= alignment AL-CENTER) (/ (- new-width (* 2 (if (> direction 0) (ceiling (/ width 2)) (floor (/ width 2))))) 2))
								(else 0)
						)
			)
			(offy		(if (> direction 0) 0 (- new-height height)))
			(index		2)
		)
		(gimp-image-resize image new-width new-height offx offy)
		(gimp-layer-resize-to-image-size drawable)
		(while (< index new-width)
			;(unless (null? progress)
			;	(gimp-progress-set-text (list-ref progress 2))
			;	(gimp-progress-update (+ (list-ref progress 0) (* (/ index new-width) (- (list-ref progress 1) (list-ref progress 0)))))
			;)
			(gimp-progress-pulse)
			(gimp-rect-select image index 0 2 new-height CHANNEL-OP-REPLACE 0 0)
			(gimp-floating-sel-anchor (car (gimp-selection-float drawable 0 (* (quotient index 2) direction))))
			(set! index (+ index 2))
		)
	)
	(gimp-image-undo-group-end image)
)

(define (shear-borders image drawable dir)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(height (car (gimp-drawable-height drawable))) 
			(width (car (gimp-drawable-width drawable)))
			(alpha? (lambda (x y) (zero? (vector-ref (cadr (gimp-drawable-get-pixel drawable x y)) 3))))
		)
		(gimp-selection-none image)
		(if (zero? dir)
			(begin
				(let* ((L2R #t) (R2L #t))
					(let* ((index (- height 1)))
						(while (>= index 0)
							(if (even? (- height index))
								(begin (set! L2R (and L2R (alpha? 0 index))) (set! R2L (and R2L (alpha? (- width 1) index))))
								(begin (set! R2L (and R2L (alpha? 0 index))) (set! L2R (and L2R (alpha? (- width 1) index))))
							)
							(set! index (- index 1))
						)
					)
					(when (or L2R R2L)
						(if R2L (gimp-image-flip image ORIENTATION-HORIZONTAL))
						(let* (	(index (- height 3)))
							(while (>= index 0)
								(gimp-rect-select image 1 index 1 1 CHANNEL-OP-ADD 0 0)
								(set! index (- index 2))
							)
						)
						(let* ((sel (selection-save image)))
							(translate image drawable -1 +1 0)
							(gimp-selection-load sel)
							(gimp-selection-translate image +1 0)
							(gimp-rect-select image 1 0 1 1 CHANNEL-OP-ADD 0 0)
							(translate image drawable -1 +0 1)
							(gimp-selection-load sel)
							(gimp-selection-translate image (- width 2) +1)
							(translate image drawable -1 -1 0)
							(gimp-image-remove-channel image sel)
						)
						(gimp-image-resize image (- width 1) height 0 0)
						(gimp-layer-resize-to-image-size drawable)
						(if R2L (gimp-image-flip image ORIENTATION-HORIZONTAL))
					)
				)
			)
			(begin
				(if (negative? dir) (gimp-image-flip image ORIENTATION-HORIZONTAL))
				(gimp-image-resize image (+ width 1) height 0 0)
				(gimp-layer-resize-to-image-size drawable)
				(let* ((index (- height 2)))
					(while (>= index 0)
						(gimp-rect-select image 0 index 1 1 CHANNEL-OP-ADD 0 0)
						(set! index (- index 2))
					)
				)	
				(translate image drawable +1 -1 0)
				(gimp-selection-none image)
				(let* ((index (- height 3)))
					(while (>= index 0)
						(gimp-rect-select image (- width 1) index 1 1 CHANNEL-OP-ADD 0 0)
						(set! index (- index 2))
					)
				)
				(let* ((sel (selection-save image)))
					(translate image drawable +1 +1 0)
					(gimp-selection-load sel)
					(gimp-selection-translate image -1 0)
					(gimp-rect-select image (- width 1) 0 1 1 CHANNEL-OP-ADD 0 0)
					(translate image drawable +1 +0 1)
					(gimp-image-remove-channel image sel)
				)
				(if (negative? dir) (gimp-image-flip image ORIENTATION-HORIZONTAL))
			)
		)
	)
	(gimp-selection-none image)
	(gimp-image-undo-group-end image)
)

(define (thicken-borders image drawable dir)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(height 	(car (gimp-drawable-height drawable))) 
			(width 		(car (gimp-drawable-width drawable)))
			(new-width	(if (positive? dir) (+ width 2) (- width 2)))
			(offx		(if (positive? dir) +1 -1))
		)
		(gimp-image-resize image new-width height offx 0)
		(gimp-layer-resize-to-image-size drawable)
		(when (positive? dir)
			(gimp-rect-select image 1 0 1 height CHANNEL-OP-REPLACE 0 0)
			(translate image drawable -1 0 1)
			(gimp-rect-select image (- new-width 2) 0 1 height CHANNEL-OP-REPLACE 0 0)
			(translate image drawable +1 0 1)
			(gimp-selection-none image)
		)
	)
	(gimp-image-undo-group-end image)		
)

(define (vertical-stretch image drawable ratio . interpolation)
	(gimp-image-undo-group-start image)
	(set! interpolation (if (null? interpolation) INTERPOLATION-NONE (car interpolation)))
	(let* 
		(
			(width 	 		(car (gimp-drawable-width 	drawable)))
			(height 		(car (gimp-drawable-height 	drawable)))
			(new-height		(ceiling (* height (cond ((positive? ratio) ratio) ((negative? ratio) (/ (abs ratio))) (else 1)))))
			(top-buffer		"")
			(bottom-buffer	"")
		)
		(gimp-rect-select image 0 0 width 1 CHANNEL-OP-REPLACE 0 0)
		(set! top-buffer (car (gimp-edit-named-copy drawable "simutrans-vertical-stretch-top")))
		(gimp-selection-translate image 0 (- height 1))
		(set! bottom-buffer (car (gimp-edit-named-copy drawable "simutrans-vertical-stretch-bottom")))
		(gimp-image-crop image width (- height 2) 0 1)
		(gimp-image-scale-full image width (- new-height 2) interpolation)
		(gimp-image-resize image width new-height 0 1)
		(gimp-layer-resize-to-image-size drawable)
		(gimp-rect-select image 0 0 width 1 CHANNEL-OP-REPLACE 0 0)
		(gimp-floating-sel-anchor (car (gimp-edit-named-paste drawable top-buffer 1)))
		(gimp-selection-translate image 0 (- new-height 1))
		(gimp-floating-sel-anchor (car (gimp-edit-named-paste drawable bottom-buffer 1)))
		(gimp-buffer-delete top-buffer)
		(gimp-buffer-delete bottom-buffer)
		(gimp-selection-none image)
	)
	(gimp-image-undo-group-end image)
)

(define (detect-shape image drawable)
	(gimp-image-undo-group-start image)
	(gimp-selection-layer-alpha drawable)
	(if (zero? (car (gimp-selection-is-empty image)))
		(begin
			(plug-in-sel2path RUN-NONINTERACTIVE image drawable)
			(let*
				(
					(shape 			SH-DEFAULT)
					(orientation 	OR-DEFAULT)
					(alignment		AL-CENTER)
					(newpath		(vector-ref (cadr (gimp-image-get-vectors image)) 0))
					(points			(gimp-vectors-stroke-get-points newpath (vector-ref (cadr (gimp-vectors-get-strokes newpath)) 0)))
					(width			(car (gimp-drawable-width  drawable)))
					(height			(car (gimp-drawable-height drawable)))
					(area 			(lambda (k)
										(set! k (append k (list (car k) (cadr k))))
										(let* ((n (length k)) (i 0) (A 0))
											(while (< i (- n 3))
												(set! A (+ A (-	(* (list-ref k (+ i 0)) (list-ref k (+ i 3))) (* (list-ref k (+ i 2)) (list-ref k (+ i 1))))))
												(set! i (+ i 2))
											)
											(/ (abs A) 2)
										)
									)
					)
					(f 				(lambda (m x c) 
										(+ (* m x) c)
									)
					)
					(i				(lambda (a1 b1 c1 a2 b2 c2)
										(list	(/ (- (* b2 c1) (* b1 c2)) (- (* a1 b2) (* a2 b1)))
												(/ (- (* a1 c2) (* a2 c1)) (- (* a1 b2) (* a2 b1)))
										)
									)
					)
					(cVER nil)		(cHOR nil)
					(cI05 nil)		(cD05 nil)
					(cI10 nil)		(cD10 nil)
					(count	0)
				)
				(while (< count (- (cadr points) 1))
					(let* 
						(
							(x (round (vector-ref (caddr points) (+ count 2)))) 
							(y (round (vector-ref (caddr points) (+ count 3))))
						)
						(set! cVER (cons  x           cVER)) ;c=x
						(set! cHOR (cons  y 		  cHOR)) ;c=y
						(set! cI05 (cons (f -0.5 x y) cI05)) ;c=y-x/2
						(set! cD05 (cons (f +0.5 x y) cD05)) ;c=y+x/2
						(set! cI10 (cons (f -1.0 x y) cI10)) ;c=y-x/2
						(set! cD10 (cons (f +1.0 x y) cD10)) ;c=y+x/2
						(set! count (+ count 6))
					)
				)
				(gimp-image-remove-vectors image newpath)
				(let* 
					(	;It must convert lines from Slope Intercept formula to Standard Form formula.
						;y=mx+c  ->  Ax+By=C  ::  A=-m  B=1  C=c
						(frk (append	(i +1.0 +0.0 (apply max cVER) +0.0 +1.0 (apply max cHOR)) 	(i +1.0 +0.0 (apply min cVER) +0.0 +1.0 (apply max cHOR)) 
										(i +1.0 +0.0 (apply min cVER) +0.0 +1.0 (apply min cHOR)) 	(i +1.0 +0.0 (apply max cVER) +0.0 +1.0 (apply min cHOR))	))
						(swk (append	(i +1.0 +0.0 (apply max cVER) -0.5 +1.0 (apply max cI05)) 	(i +1.0 +0.0 (apply min cVER) -0.5 +1.0 (apply max cI05)) 
										(i +1.0 +0.0 (apply min cVER) -0.5 +1.0 (apply min cI05)) 	(i +1.0 +0.0 (apply max cVER) -0.5 +1.0 (apply min cI05))	))
						(ewk (append	(i +1.0 +0.0 (apply max cVER) +0.5 +1.0 (apply max cD05)) 	(i +1.0 +0.0 (apply min cVER) +0.5 +1.0 (apply max cD05)) 
										(i +1.0 +0.0 (apply min cVER) +0.5 +1.0 (apply min cD05)) 	(i +1.0 +0.0 (apply max cVER) +0.5 +1.0 (apply min cD05))	))
						(flk (append	(i -0.5 +1.0 (apply max cI05) +0.5 +1.0 (apply max cD05)) 	(i -0.5 +1.0 (apply min cI05) +0.5 +1.0 (apply max cD05)) 
										(i -0.5 +1.0 (apply min cI05) +0.5 +1.0 (apply min cD05)) 	(i -0.5 +1.0 (apply max cI05) +0.5 +1.0 (apply min cD05))	))
						(ssk (append	(i -0.5 +1.0 (apply max cI05) +1.0 +1.0 (apply max cD10)) 	(i -0.5 +1.0 (apply min cI05) +1.0 +1.0 (apply max cD10)) 
										(i -0.5 +1.0 (apply min cI05) +1.0 +1.0 (apply min cD10)) 	(i -0.5 +1.0 (apply max cI05) +1.0 +1.0 (apply min cD10))	))
						(esk (append	(i +0.5 +1.0 (apply max cD05) -1.0 +1.0 (apply max cI10)) 	(i +0.5 +1.0 (apply min cD05) -1.0 +1.0 (apply max cI10)) 
										(i +0.5 +1.0 (apply min cD05) -1.0 +1.0 (apply min cI10)) 	(i +0.5 +1.0 (apply max cD05) -1.0 +1.0 (apply min cI10))	))
						(nsk (append	(i -0.5 +1.0 (apply max cI05) +0.0 +1.0 (apply max cHOR)) 	(i -0.5 +1.0 (apply min cI05) +0.0 +1.0 (apply max cHOR)) 
										(i -0.5 +1.0 (apply min cI05) +0.0 +1.0 (apply min cHOR)) 	(i -0.5 +1.0 (apply max cI05) +0.0 +1.0 (apply min cHOR))	))
						(wsk (append	(i +0.5 +1.0 (apply max cD05) +0.0 +1.0 (apply max cHOR)) 	(i +0.5 +1.0 (apply min cD05) +0.0 +1.0 (apply max cHOR)) 
										(i +0.5 +1.0 (apply min cD05) +0.0 +1.0 (apply min cHOR)) 	(i +0.5 +1.0 (apply max cD05) +0.0 +1.0 (apply min cHOR))	))
						(lsk (list (area frk) (area swk) (area ewk) (area flk) (area ssk) (area esk) (area nsk) (area wsk)))
					)
					(debug	"FR" (area frk)		"SW" (area swk) 	"EW" (area ewk) 	"FL" (area flk)
							"SS" (area ssk) 	"ES" (area esk) 	"NS" (area nsk) 	"WS" (area wsk) )
					(case (length (cdr (member (apply min lsk) (reverse lsk))))
						((0) (set! shape SH-FRONT)	(set! orientation OR-SOUTH)) ;frk
						((1) (set! shape SH-WALL)	(set! orientation OR-SOUTH)) ;swk
						((2) (set! shape SH-WALL)	(set! orientation OR-EAST))  ;ewk
						((3) (set! shape SH-TILE)	(set! orientation OR-SOUTH)) ;flk
						((4) (set! shape SH-SLOPE-2)	(set! orientation OR-SOUTH)) ;ssk
						((5) (set! shape SH-SLOPE-2)	(set! orientation OR-EAST))	 ;esk	
						((6) (set! shape SH-SLOPE-2)	(set! orientation OR-NORTH)) ;
						((7) (set! shape SH-SLOPE-2)	(set! orientation OR-WEST))  ;
					)
				)
				(when (= shape SH-WALL)
					(let* 
						(
							(by (list-ref (gimp-selection-bounds image) 4))
							(?  (gimp-rect-select image 0 (- by 1) width 1 CHANNEL-OP-INTERSECT 0 0))
							(bl (list-ref (gimp-selection-bounds image) 1))
							(br (list-ref (gimp-selection-bounds image) 3))
						)
						(set! alignment
							(if (= orientation OR-SOUTH)
								(if (odd? (- width bl)) (if (odd? width) AL-LEFT  AL-CENTER) AL-RIGHT)
								(if (odd? br) 			(if (odd? width) AL-RIGHT AL-CENTER) AL-LEFT)
							)
						)
					)
				)
				(gimp-selection-none image)
				(gimp-image-undo-group-end image)				
				(gimp-progress-end)
				(list shape orientation alignment)
			) ;end of let*
		) ;end of begin
		(begin ;ELSE
			(gimp-image-undo-group-end image)				
			(gimp-message "The source image is empty or doesn't contain a valid shape.")
			(list 0 0 0)
		)
	) ;end of if
)

;;
;; ============================================================================================
;; TILE UTILITIES

(define (script-fu-simutrans-move-multilayer image drawable offx offy mask visible-only)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(selection	(selection-save image))
			(active-layer (car (gimp-image-get-active-layer image)))
			
		)
		(for-each-layer image (list mask visible-only)
			(lambda (this-layer)
				;Load selection for this layer
				(gimp-selection-load selection)
				;Check if selection is out of this-layer
				(unless (zero? (car (gimp-drawable-mask-intersect this-layer)))
					;Float, move & anchor selection
					(gimp-floating-sel-anchor (car (gimp-selection-float this-layer offx offy)))
				)
			) ;end of lambda (this-layer)
		) ;end of for-each-layer
		
		(unless (zero? selection) 
			(gimp-selection-load selection)
			(gimp-selection-translate image offx offy)
			(gimp-image-remove-channel image selection)
		)
		(unless (= active-layer -1) ;reset active layer
			(gimp-image-set-active-layer image active-layer)
		)		   
	)
	(gimp-image-undo-group-end image)   
	(gimp-displays-flush) 
)

(define (script-fu-simutrans-transform-multilayer image drawable transf mask visible-only)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(selection	  (selection-save image))
			(active-layer (car (gimp-image-get-active-layer image)))
			
		)
		(for-each-layer image (list mask visible-only)
			(lambda (this-layer)
				;Load selection for this layer
				(gimp-selection-load selection)
				;Check if selection is out of this-layer
				(unless (zero? (car (gimp-drawable-mask-intersect this-layer)))
					;Floats, transform & anchor selection
					(cond 
						((= transf TR-ROTATE-90 ) (gimp-drawable-transform-rotate-simple this-layer ROTATE-90  TRUE 0 0 TRUE))
						((= transf TR-ROTATE-270) (gimp-drawable-transform-rotate-simple this-layer ROTATE-270 TRUE 0 0 TRUE))
						((= transf TR-ROTATE-180) (gimp-drawable-transform-rotate-simple this-layer ROTATE-180 TRUE 0 0 TRUE))
						((= transf TR-FLIP-HOR  ) (gimp-drawable-transform-flip-simple this-layer ORIENTATION-HORIZONTAL TRUE 0 TRUE))
						((= transf TR-FLIP-VER  ) (gimp-drawable-transform-flip-simple this-layer ORIENTATION-VERTICAL   TRUE 0 TRUE))
						((= transf TR-FLIP-VER  ) (gimp-drawable-transform-flip-simple this-layer ORIENTATION-VERTICAL   TRUE 0 TRUE))
						((= transf TR-CUSTOM    ) (script-fu-swap-colors image this-layer TRUE 0 0))
					)
					(let ((floating-layer (car (gimp-image-get-floating-sel image))))
						(when (positive? floating-layer) (gimp-floating-sel-anchor floating-layer)) 
					)
				)
			) ;end of lambda (this-layer)
		) ;end of for-each-layer
		
		(unless (zero? selection) 
			(gimp-selection-load selection)
			(gimp-image-remove-channel image selection)
		)
		(unless (= active-layer -1) ;reset active layer
			(gimp-image-set-active-layer image active-layer)
		)		   
	)
	(gimp-image-undo-group-end image)   
	(gimp-displays-flush) 
)

(define (script-fu-simutrans-copy-cut-multilayer image drawable mode mask visible-only)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(active-layer 		(car (gimp-image-get-active-layer image)))
			(base-buffer-name	"simutrans-copy-cut-multilayer")
			(temp-buffer-name	base-buffer-name)
			(temp-image			0)
			(temp-layer			0)
		)
			
		;Delete existing buffers
		(for-each 
			(lambda (this-buffer) 
				(gimp-buffer-delete this-buffer)
			) ;end of lambda (this-buffer)
				(cadr (gimp-buffers-get-list (buffer-name -1)))
		) ;end of for-each
		
		;Loop through all layers
		(for-each-layer image (list mask visible-only)
			(lambda (this-layer)
			
				;Check if selection is out of this-layer
				(unless (zero? (car (gimp-drawable-mask-intersect this-layer)))
				
					;Check mode: 0=copy 1=cut
					(set! temp-buffer-name 
						(car
							(apply 
								(if (zero? mode) gimp-edit-named-copy gimp-edit-named-cut) 
								(list this-layer base-buffer-name)
							)
						)
					) ;end of set! temp-buffer-name
					
					;Edit in temp image
					(edit-buffer-in-temporary-image temp-buffer-name 
						(lambda (temp-image temp-layer)
							;Select non-transparent area
							(gimp-selection-layer-alpha temp-layer)
							;Check if selection is empty
							(when (zero? (car (gimp-selection-is-empty temp-image)))
								;Select all
								(gimp-selection-all temp-image)						
								;Copy temp to named buffer
								(gimp-edit-named-copy temp-layer (buffer-name this-layer))
							) ;end of when
							;Delete temp-image, no temp-buffer is returned
							(gimp-image-delete temp-image)		
						) ;end of lambda (temp-image temp-layer)
					)			
					
				) ;end of unless
				;Update progress bar
				(gimp-progress-update (car (stats this-layer (vector->list (cadr (gimp-image-get-layers image))))))
			) ;end of lambda (this-layer)
		) ;end of for-each-layer
		
		;Update progress bar
		(gimp-progress-update 1)   
		
		;Reset active layer	
		(unless (= active-layer -1) 
			(gimp-image-set-active-layer image active-layer)
		) ;end of unless
	)
		
	(gimp-image-undo-group-end image)	
	(gimp-progress-end)
	(gimp-displays-flush)
)

(define (script-fu-simutrans-paste-multilayer image drawable)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(buffer-list  '())
			(selection	(selection-save image))	
			(active-layer (car (gimp-image-get-active-layer image)))
		)
		(for-each-layer image (string)
			(lambda (this-layer)
				(set! buffer-list (gimp-buffers-get-list (buffer-name this-layer)))
				(unless (zero? (car buffer-list))
					(gimp-floating-sel-anchor (car (gimp-edit-named-paste this-layer (caadr buffer-list) 1)))
				)
			) ;end of lambda (this-layer)
		) ;end of for-each-layer
		(unless (zero? selection) ;reset selection 
			(gimp-selection-load selection)
			(gimp-image-remove-channel image selection)
		)
		(unless (= active-layer -1) ;reset active layer
			(gimp-image-set-active-layer image active-layer)
		)
	)
	(gimp-image-undo-group-end image) 
	(gimp-displays-flush)   
)

(define (script-fu-simutrans-set-grid image drawable tilesize resize-image resize-layers)
	(let* 
		(
			(spacing 
				(case tilesize
					((0) 32)
					((1) 48)
					((2) 64)
					((3) 96)
					((4) 128)
					((5) 160)
					((6) 192)				
				)
			)
			(height		(car (gimp-image-height image)))
			(width	  	(car (gimp-image-width  image)))
			(new-height 0)
			(new-width  0)
		)
		
		(gimp-image-undo-group-start image)		
		
		(unless (zero? resize-image)
			(if (zero? (modulo height spacing)) (set! new-height height) (set! new-height (* (+ (quotient height spacing) 1) spacing)))
			(if (zero? (modulo width  spacing)) (set! new-width  width ) (set! new-width  (* (+ (quotient width  spacing) 1) spacing)))
			(if (or (<> height new-height) (<> width new-width)) (gimp-image-resize image new-width new-height 0 0))
			(unless (zero? resize-layers)
				(for-each-layer image (string)
					(lambda (this-layer)
						(if (and 
								(= (car (gimp-drawable-width this-layer )) width ) 
								(= (car (gimp-drawable-height this-layer)) height)
							)
							(gimp-layer-resize-to-image-size this-layer)
						)
					)
				)
			)
		)
		
		(gimp-image-grid-set-spacing image spacing spacing)
		(gimp-image-grid-set-offset image 0 0)	
		
		(gimp-image-undo-group-end image)
		(gimp-progress-end)
		(gimp-displays-flush)
	)
)

(define (script-fu-simutrans-isometric-surface image drawable src-shape src-orientation src-alignment transf dst-shape dst-orientation dst-alignment zoom)
	(gimp-image-undo-group-start image)
	(let* 
		(
			(temp-buffer-name	"simutrans-isometric-shape")
			(autodetect			nil)
			(prog				nil)
			(info				"")
			(interpolate		0)
			(clip-result		0)
		)

		;Cut to temp buffer
		;==================
		(unless (zero? (car (gimp-selection-is-empty image))) (gimp-selection-all image))
		(set! temp-buffer-name 
			(car
				(gimp-edit-named-cut drawable temp-buffer-name)
			)
		) ;end of set! temp-buffer-name
		
		;Edit in temp image
		;==================
		(set! temp-buffer-name 
			(car
				(edit-buffer-in-temporary-image temp-buffer-name 
					(lambda (temp-image temp-layer)
						(crop-to-alpha temp-image temp-layer)
						(when (or (= src-shape SH-DEFAULT) (= src-orientation OR-DEFAULT))
							(let* ((autodetect			(detect-shape temp-image temp-layer)))
							(debug "Autodetected shape" autodetect)
							(set! src-shape 		(list-ref autodetect 0))
							(set! src-orientation 	(list-ref autodetect 1))
							(set! src-alignment 	(list-ref autodetect 2))
							)
						)
						;(if (= src-alignment AL-DEFAULT) 
						;	(cond
						;		((= src-orientation OR-SOUTH)	(set! src-alignment AL-RIGHT))
						;		((= src-orientation OR-EAST)	(set! src-alignment AL-LEFT))
						;		(else 							(set! src-alignment AL-CENTER))
						;	)
						;)
						(if (= src-shape SH-DEFAULT) ;Autodetect failed 
							(gimp-message "Impossible to autodetect the source shape, orientation or alignment. Please select an orientation and an alignment and try again.")
							(begin
								(if (= dst-shape SH-DEFAULT) 		(set! dst-shape 		src-shape))
								(if (= dst-orientation OR-DEFAULT)	(set! dst-orientation 	src-orientation))
								(if (= dst-alignment AL-DEFAULT) 	(set! dst-alignment 	src-alignment))
								(set! info (string-append	(list-ref (SH-LIST) src-shape) " " (list-ref (OR-LIST) src-orientation) " " 
															(if (> transf TR-NONE) (list-ref TR-LIST transf) "") " -> "
															(list-ref (SH-LIST) dst-shape) " " (list-ref (OR-LIST) dst-orientation)))
								;(gimp-progress-set-text info)
								
								;If not front image, transform the surface back to front image
								;=============================================================
								(let*
									(
										(horizontal-shear 
											(cond
												((= src-shape SH-FRONT)   +0.0)
												((= src-shape SH-TILE)    +1.0)
												((= src-shape SH-WALL)    +0.0)
												((= src-shape SH-SLOPE-1) +2.0)
												((= src-shape SH-SLOPE-2) +1.5)
											)
										)
										(vertical-shear   (if (= src-shape SH-FRONT)   +0.0   -0.5))
									)
									(gimp-drawable-transform-shear-default temp-layer ORIENTATION-VERTICAL   (* (car (gimp-drawable-width  temp-layer)) vertical-shear  ) interpolate clip-result)
									(gimp-drawable-transform-shear-default temp-layer ORIENTATION-HORIZONTAL (* (car (gimp-drawable-height temp-layer)) horizontal-shear) interpolate clip-result)
								)

								;If a transformation is selected, apply it now
								;=============================================
								(cond 
									((= transf TR-ROTATE-90 ) (gimp-image-rotate temp-image ROTATE-90 ))
									((= transf TR-ROTATE-270) (gimp-image-rotate temp-image ROTATE-270))
									((= transf TR-ROTATE-180) (gimp-image-rotate temp-image ROTATE-180))
									((= transf TR-FLIP-HOR  ) (gimp-image-flip temp-image ORIENTATION-HORIZONTAL))
									((= transf TR-FLIP-VER  ) (gimp-image-flip temp-image ORIENTATION-VERTICAL  ))
								)
								
								;Transform the image into the selected surface
								;=============================================
								(let*
									(
										(vertical-stretch
											(cond
												((= dst-shape SH-SLOPE-1)  +1.25)
												((= dst-shape SH-SLOPE-2)  +1.50)
												(else 					   +1.00)
											)
										)										
										(vertical-shear
											(cond
												((= dst-shape SH-FRONT)    +0.00)
												(else                      +0.50)
											)
										)										
										(horizontal-shear
											(cond
												((= dst-shape SH-FRONT)    +0.00)
												((= dst-shape SH-WALL)     +0.00)
												(else (/ -1.00 vertical-stretch))
											)
										)										
									)
									(gimp-layer-scale-full temp-layer 
										(car (gimp-drawable-width  temp-layer)) 
										(* (car (gimp-drawable-height temp-layer)) vertical-stretch) 
										FALSE interpolate
									)
									(gimp-drawable-transform-shear-default temp-layer 
										ORIENTATION-HORIZONTAL 
										(* (car (gimp-drawable-height temp-layer)) horizontal-shear) 
										interpolate clip-result
									)
									(gimp-drawable-transform-shear-default temp-layer 
										ORIENTATION-VERTICAL   
										(* (car (gimp-drawable-width  temp-layer)) vertical-shear  ) 
										interpolate clip-result
									)
								)
								;(gimp-progress-update 1.000)
							)
						)
					) ;end of lambda (temp-image temp-layer)
				)
			)
		) ;end of set! temp-buffer-name 

		;Paste back buffer
		;=================
		(gimp-edit-named-paste drawable temp-buffer-name 1)
		(gimp-selection-none image)
		(gimp-buffer-delete temp-buffer-name)

	)
	(gimp-image-undo-group-end image)	
	;(gimp-progress-end)
	(gimp-displays-flush)
)

;;
;; ============================================================================================
;; COLOR UTILITIES

(define (script-fu-simutrans-export image drawable suffix flatten threshold)
	(let* 
		(
			(transparent-color  '(231 255 255))
			
			(image-filename	 (car (gimp-image-get-filename image)))
			(base-filename	  (car (strbreakup image-filename ".")))
			(extension		  ".png")
			
			(export-buffer-name (car (gimp-edit-named-copy-visible image "STEXPORT")))
			(export-image	   (car (gimp-edit-named-paste-as-new export-buffer-name)))
			(layer			  (car (vector->list (cadr (gimp-image-get-layers export-image)))))
						
		)
		(gimp-context-push)
		(gimp-context-set-background transparent-color)	  
		(gimp-image-undo-disable export-image)
		(gimp-buffer-delete export-buffer-name)
		
		(plug-in-threshold-alpha 
			RUN-NONINTERACTIVE 
			export-image 
			layer 
			threshold
		)
			
		(unless (zero? flatten)
			(gimp-layer-flatten layer)
		)
		
		(file-png-save-defaults 
			RUN-NONINTERACTIVE 
			export-image 
			layer 
			(string-append base-filename suffix extension)
			(string-append base-filename suffix extension) 
		)
		
		(gimp-image-delete export-image)   
				
		(gimp-context-pop)	 
		(gimp-progress-end)
		(gimp-displays-flush)
			
	)
	
)

(define (script-fu-swap-colors image drawable use-context color1 color2)
	(gimp-image-undo-group-start image)
	(gimp-context-push)
	(unless (zero? use-context)
		(set! color1 (car (gimp-context-get-foreground)))
		(set! color2 (car (gimp-context-get-background)))
	)
	(let* 
		(
			(selection (selection-save image))
			(selection-by-color-save (lambda (color)
				(selection-by-color drawable color 0 selection)
				(if (zero? (car (gimp-selection-is-empty image)))
					(car (gimp-selection-save image))
					0
			)))
			(color1-selection (selection-by-color-save color1))
			(color2-selection (selection-by-color-save color2))
		)
		(gimp-progress-update 0.25)
		(unless (zero? color1-selection)
			(gimp-selection-load color1-selection)
			(gimp-context-set-foreground color2)
			(gimp-edit-fill drawable FOREGROUND-FILL)
			(gimp-image-remove-channel image color1-selection)	
		)   
		(gimp-progress-update 0.50)		 
		(unless (zero? color2-selection)
			(gimp-selection-load color2-selection)
			(gimp-context-set-foreground color1)
			(gimp-edit-fill drawable FOREGROUND-FILL)
			(gimp-image-remove-channel image color2-selection)	
		)		
		(gimp-progress-update 0.75)			
		(unless (zero? selection)
			(gimp-selection-load selection)
			(gimp-image-remove-channel image selection)	
		)
		(gimp-progress-update 1)			
		
	)
	(gimp-context-pop)
	(gimp-image-undo-group-end image)
	(gimp-progress-end)
	(gimp-displays-flush)
)

(define (script-fu-simutrans-special-colors-helper 
			image 
			drawable 
			operation 
			non-darkening-greys
			window-colors
			primary-player-colors
			secondary-player-colors
			lights
			transparent-color
			layers-option
			select-mode
			threshold
			lookup-file
		)
	(gimp-image-undo-group-start image)
	(gimp-context-push)
	(let* 
		(	  
			(OP-SELECT  0)	
			(OP-REMOVE  1)				
			(OP-REPAIR  2)				
			(OP-LIGHTEN 3)	
			(OP-DARKEN  4)	
			(OP-LOOKUP  5)
			
			(selection (selection-save image))
			(layer-list 
				(cond 
					((or 
						(= layers-option 0)   ;This layer
						(= layers-option 2))  ;Sample merged (only select)
						(list drawable)) 
					((= layers-option 1)	  ;All layers
						(vector->list (cadr (gimp-image-get-layers image)))) 
				))
			
			(color-set-list   '())
			
			(layer-stats	  0)
			(color-set-stats  0)
			(color-stats	  0)
			
			(lookup-image	  0)
			(lookup-vertical  0)
		)
				
		
		;If Sample merged, force Select operation
		(if (= layers-option 2)
			(set! operation OP-SELECT)
		)
			
		;Add selected special color sets to the list
		(unless (zero? non-darkening-greys) 	(set! color-set-list (cons non-darkening-greys-list	  	color-set-list)))
		(unless (zero? window-colors) 			(set! color-set-list (cons window-colors-list			color-set-list)))
		(unless (zero? primary-player-colors) 	(set! color-set-list (cons primary-player-colors-list	color-set-list)))
		(unless (zero? secondary-player-colors)	(set! color-set-list (cons secondary-player-colors-list color-set-list)))
		(cond 
			((= operation OP-DARKEN)) 	;do nothing
			((= operation OP-LIGHTEN) 	;reverse color sets from lighter to darker
				(set! color-set-list (map reverse color-set-list)))
			(else					 	;add additional color sets to the list
				(unless (zero? lights)		 		(set! color-set-list (cons lights-list				color-set-list)))
				(unless (zero? transparent-color) 	(set! color-set-list (cons transparent-color-list	color-set-list)))
			)
		)	
		
		;Special initialization for some operations
		(cond 
			;if Select operation, select none
			((= operation OP-SELECT) 
				(gimp-selection-none image))
			;if Lookup operation, open lookup image and sets orientation
			((= operation OP-LOOKUP) 
				(set! lookup-image (car (gimp-file-load RUN-NONINTERACTIVE lookup-file lookup-file)))
				(set! lookup-vertical (if (> (car (gimp-image-height lookup-image)) (car (gimp-image-width lookup-image))) TRUE FALSE)))
		)
			  
		;Repeat for each layer
		(for-each
			(lambda (this-layer)
				(set! layer-stats (stats this-layer layer-list))
		
				;Repeat for each selected color set
				(for-each 
					(lambda (this-color-set)
						(set! color-set-stats (stats this-color-set color-set-list))					
				
						;Repeat for each color in the set	
						(for-each 
							(lambda (this-color)
								(set! color-stats (stats this-color this-color-set))
							
								;Perform chosen operation
								(cond
									
									;Select operation
									((= operation OP-SELECT) 
										(gimp-by-color-select this-layer this-color 0 CHANNEL-OP-ADD FALSE FALSE 0 
											(if (= layers-option 2) TRUE FALSE) ;Sample merged
										)
									)
									
									;Remove operation
									((= operation OP-REMOVE) 
										(selection-by-color this-layer this-color 0 selection)
										(selection-fill this-layer 
											(map 
												(lambda (n) (if (< n 128) (+ n 1) (- n 1))) 
												this-color
											))
									)
									
									;Repair operation   
									((= operation OP-REPAIR)  
										(selection-by-color this-layer this-color threshold selection)
										(selection-fill this-layer this-color)
									)
									
									;Lighten/Darken operation   
									((or (= operation OP-LIGHTEN) (= operation OP-DARKEN)) 
										(let* ((prev-color (cdr (member this-color (reverse this-color-set)))))
											(unless (null? prev-color)
												(selection-by-color this-layer this-color 0 selection)
												(selection-fill this-layer (car prev-color))
											)
										)
									)
										
									;Lookup operation   
									((= operation OP-LOOKUP)
										(let* ((lu-color (color-lookup lookup-image this-color lookup-vertical)))
											(unless (null? lu-color)
												(selection-by-color this-layer lu-color 0 selection)
												(selection-fill this-layer this-color)
											)
										)
									)
										
								) ;end of case operation
								
								;Update progress bar
								(gimp-progress-update 
									(+	 (car layer-stats) 
										(* (car color-set-stats) (cdr layer-stats)				   )
										(* (car color-stats)	 (cdr layer-stats) (cdr color-set-stats) ))
								)   
								
							) ;end of lambda (this-color)
								this-color-set
						) ;end of for-each
							
					) ;end of lambda (this-color-set)   
						color-set-list
				) ;end of for-each	
					
			) ;end of lambda (this-layer)
				layer-list
		) ;end of for-each
			
		;Update progress bar
		(gimp-progress-update 1)	
		
		;Special termination for some operations
		(cond 
			;if Lookup operation, close lookup image
			((= operation OP-LOOKUP) 
				(gimp-image-delete lookup-image))
		)
			
		;Reset or mask selection		
		(if (zero? selection)
		
			;No initial selection
			(unless (= operation OP-SELECT) 
				(gimp-selection-none image)
			)
		
			;Existing selection
			(begin
				(if (= operation OP-SELECT)
					(case select-mode
						((0)) ;do nothing											 	;Replace current selection
						((1) (gimp-selection-combine selection CHANNEL-OP-INTERSECT))	;Only in current selection
						((2) (gimp-selection-combine selection CHANNEL-OP-ADD))	   		;Add to current selection
						((3) (gimp-selection-invert image)								;Subtract from current selection
							 (gimp-selection-combine selection CHANNEL-OP-INTERSECT))
					)
					(gimp-selection-load selection)
				)
				(gimp-image-remove-channel image selection)	
			)
		)
							
			(gimp-progress-end)
	)
	(gimp-context-pop)
	(gimp-image-undo-group-end image)
	(gimp-displays-flush)
)


;;
;; ============================================================================================
;; REGISTER UTILITIES

(script-fu-register "script-fu-simutrans-special-colors-helper"
	"Special Colors _Helper..."
	"Select, remove or repair Simutrans special colors"
	"Fabio Gonella"
	"Fabio Gonella"
	"March 2012"
	"RGB*"
	SF-IMAGE	  "Image"	0
	SF-DRAWABLE   "Drawable" 0
	
	SF-OPTION	 
		"_Operation to perform" 
		'(
			"Select special colors" 
			"Remove special colors" 
			"Repair special colors"
			"Lighten special colors"
			"Darken special colors"
			"Convert to special colors"
		)
	
	SF-TOGGLE	 "_Non-darkening greys"				  	TRUE
	SF-TOGGLE	 "_Windows"							  	TRUE
	SF-TOGGLE	 "Player colors (_Primary)"			  	TRUE
	SF-TOGGLE	 "Player colors (_Secondary)"			TRUE
	SF-TOGGLE	 "_Lights (except lighten/darken)"	   	FALSE
	SF-TOGGLE	 "_Transparent (except lighten/darken)"	FALSE
	
	SF-OPTION	 
		"_Apply to" 
		'(
			"This layer"
			"All layers" 
			"Sample merged (only select)"
		)  
			  
	SF-OPTION	 
		"Selection _mode (only select)" 
		'(
			"Replace current selection"
			"Only in current selection" 
			"Add to current selection" 
			"Subtract from current selection"
		)
			
	SF-ADJUSTMENT "Th_reshold (only repair)"			  '(15 0 255 1 10 1 0)
	
	SF-FILENAME "Look_up image (only convert)"			(string-append "" gimp-data-directory "/scripts/images/TTD.png")
)

(script-fu-register "script-fu-simutrans-export"
	"_Export with transparent background..."
	"Export to PNG adding a transparent special color background"
	"Fabio Gonella"
	"Fabio Gonella"
	"March 2012"
	"RGB*"
	SF-IMAGE	  "Image"	   0
	SF-DRAWABLE   "Drawable"	0
	SF-STRING	 "_Custom suffix (e.g. -01)" ""
	SF-TOGGLE	 "_Flatten Alpha Channel" TRUE
	SF-ADJUSTMENT "Alpha _Threshold" '(127 0 255 1 10 1 0)	
)

(script-fu-register "script-fu-simutrans-move-multilayer"
	"_Move multilayer..."
	"Move selection through all layers"
	"Fabio Gonella"
	"Fabio Gonella"
	"April 2012"
	""
	SF-IMAGE	  "Image"	   0
	SF-DRAWABLE   "Drawable"	0
	SF-ADJUSTMENT "X Offset" '(0 -4096 4096 1 10 0 1) 
	SF-ADJUSTMENT "Y Offset" '(0 -4096 4096 1 10 0 1) 
	SF-STRING	 "_Filter layers (reg. exp.)" ""
	SF-TOGGLE	 "Only _visible layers" FALSE
)

(script-fu-register "script-fu-simutrans-transform-multilayer"
	"_Transform multilayer..."
	"Transform selection through all layers"
	"Fabio Gonella"
	"Fabio Gonella"
	"October 2012"
	""
	SF-IMAGE	"Image"	   0
	SF-DRAWABLE "Drawable"	0
	SF-OPTION	"_Transformation" (append TR-LIST '("Swap foreground/background colors"))
	SF-STRING	"_Filter layers (reg. exp.)" ""
	SF-TOGGLE	"Only _visible layers" FALSE
)
	
(script-fu-register "script-fu-simutrans-copy-cut-multilayer"
	"_Copy/Cut multilayer..."
	"Copy selection through all layers"
	"Fabio Gonella"
	"Fabio Gonella"
	"April/May 2012"
	""
	SF-IMAGE	  "Image"	   0
	SF-DRAWABLE   "Drawable"	0
	SF-OPTION	 "_Option"  '("Copy multilayer" "Cut multilayer")  
	SF-STRING	 "_Filter layers (reg. exp.)" ""
	SF-TOGGLE	 "Only _visible layers" FALSE
)

(script-fu-register "script-fu-simutrans-paste-multilayer"
	"_Paste multilayer"
	"Copy selection through all layers"
	"Fabio Gonella"
	"Fabio Gonella"
	"April 2012"
	""
	SF-IMAGE	  "Image"	   0
	SF-DRAWABLE   "Drawable"	0
)

(script-fu-register "script-fu-simutrans-isometric-surface"
	"Isometric _Surface Helper..."
	"Create an isometric wall from selection or transform an existing wall..."
	"Fabio Gonella"
	"Fabio Gonella"
	"June 2012"
	""
	SF-IMAGE	 	"Image"	   					0
	SF-DRAWABLE  	"Drawable"					0
	SF-OPTION		"Source _Shape"				(SH-LIST "Auto")
	SF-OPTION		"Source _Orientation"		(OR-LIST "Auto")
	SF-OPTION		"Source _Alignment"			(AL-LIST "Auto")
	SF-OPTION		"_Transformation"			 TR-LIST  	
	SF-OPTION		"Destination S_hape"		(SH-LIST "Auto")
	SF-OPTION		"Destination O_rientation"	(OR-LIST "Auto")
	SF-OPTION		"Destination A_lignment"	(AL-LIST "Auto")
	SF-ADJUSTMENT 	"S_mooth result" 		   '(0 0 2 1 1 1 0)	
)

(script-fu-register "script-fu-swap-colors"
	"_Swap two colors..."
	"Swap two colors in selection"
	"Fabio Gonella"
	"Fabio Gonella"
	"March 2012"
	"RGB*"
	SF-IMAGE	  "Image"	0
	SF-DRAWABLE   "Drawable" 0
	SF-TOGGLE	 "Use foreground/background colors" TRUE
	SF-COLOR	  "Color _1" (car (gimp-context-get-foreground))
	SF-COLOR	  "Color _2" (car (gimp-context-get-background))	
)
	
(script-fu-register "script-fu-simutrans-set-grid"
    "Set tiles _grid..."
    "Set grid for chosen tileset size"
    "Fabio Gonella"
    "Fabio Gonella"
    "March/June 2012"
    ""
    SF-IMAGE      "Image"       	0
    SF-DRAWABLE   "Drawable"    	0
    SF-OPTION     "Tile _Size"  	'("32" "48" "64" "96" "128" "160" "192")    
    SF-TOGGLE     "Resize _Image"	FALSE
	SF-TOGGLE     "Resize _Layers" 	FALSE
)
	
(script-fu-menu-register "script-fu-simutrans-special-colors-helper"
	"<Image>/Si_mutrans/Color Tools" )

(script-fu-menu-register "script-fu-simutrans-export"
	"<Image>/Si_mutrans/Image Tools" )

(script-fu-menu-register "script-fu-simutrans-move-multilayer"
	"<Image>/Si_mutrans/Drawing Tools" )

	(script-fu-menu-register "script-fu-simutrans-transform-multilayer"
	"<Image>/Si_mutrans/Drawing Tools" )

(script-fu-menu-register "script-fu-simutrans-copy-cut-multilayer"
	"<Image>/Si_mutrans/Drawing Tools" )

(script-fu-menu-register "script-fu-simutrans-paste-multilayer"
	"<Image>/Si_mutrans/Drawing Tools" )

(script-fu-menu-register "script-fu-simutrans-isometric-surface"
	"<Image>/Si_mutrans/Isometric Tools" )

(script-fu-menu-register "script-fu-swap-colors"
	"<Image>/Si_mutrans/Color Tools" )
	
(script-fu-menu-register "script-fu-simutrans-set-grid"
    "<Image>/Si_mutrans/Image Tools" )	
	
