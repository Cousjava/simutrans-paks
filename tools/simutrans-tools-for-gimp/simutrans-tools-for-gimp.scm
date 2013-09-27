;; SIMUTRANS TOOLS FOR GIMP 0.9
;; ============================================================================================
;;
;; ver. 0.9 - 27/09/2013
;; ADD Slope shading tool
;;
;; ver. 0.8 - 01/07/2013
;; CHG Isometric surface tool transforms a front image into any slope
;;     (tool completely reworked)
;;
;; ver. 0.7 - unreleased
;; ADD Multilayer transformtion tool (rotate & flip)
;; CHG Multilayer tools can filter for visible/not visible
;; CHG Swap Colors default to fore/back colors
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

(define slope-shading-colors-list 
	'((128 128 128)		; Front image	-	(128 128 128) means no lightning is applied!
	  (128 128 128)		; South wall 
	  (128 128 128)		; East Wall
	  (128 128 128)		; Floor tile 
	  (166 166 166)		; South slope 25%
	  (133 133 133)		; East slope 25% 
	  (106 106 106)		; North slope 25% 
	  (131 131 131)		; West slope 25% 
	  (188 188 188)		; South slope 50% 
	  (123 123 123)		; East slope 50% 
	  ( 68  68  68)		; North slope 50% 
	  (118 118 118)		; West slope 50% 
	  (128 128 128)		; South slope 75% 
	  (128 128 128)		; East slope 75% 
	  (128 128 128)		; North slope 75% 
	  (128 128 128)))	; West slope 75% 
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
(define  TR-LIST 
	'("None" "Rotate 90° clockwise" "Rotate 90° counter-clockwise" "Rotate 180°" "Flip Horizontally" "Flip Vertically"))
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

(define (detect-shape image drawable)
	(gimp-image-undo-group-start image)
	(gimp-selection-layer-alpha drawable)
	(if (zero? (car (gimp-selection-is-empty image)))
		(begin
			(plug-in-sel2path RUN-NONINTERACTIVE image drawable)
			(let*
				(
					(newpath	(vector-ref (cadr (gimp-image-get-vectors image)) 0))
					(points		(gimp-vectors-stroke-get-points newpath (vector-ref (cadr (gimp-vectors-get-strokes newpath)) 0)))
					(area 		(lambda (k)
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
					(f 			(lambda (m x c) 
									(+ (* m x) c)
								)
					)
					(i			(lambda (a1 b1 c1 a2 b2 c2)
									(list	(/ (- (* b2 c1) (* b1 c2)) (- (* a1 b2) (* a2 b1)))
											(/ (- (* a1 c2) (* a2 c1)) (- (* a1 b2) (* a2 b1)))
									)
								)
					)
					(cVERT nil)		(c0000 nil)
					(cI025 nil)		(cD025 nil)
					(cI050 nil)		(cD050 nil)
					(cI075 nil)		(cD075 nil)
					(cI100 nil)		(cD100 nil)
					(cI125 nil)		(cD125 nil)
					(cI150 nil)		(cD150 nil)
					(count	0)
				)
				(while (< count (- (cadr points) 1))
					(let* 
						(
							(x (round (vector-ref (caddr points) (+ count 2)))) 
							(y (round (vector-ref (caddr points) (+ count 3))))
						)
						(set! cVERT (cons  x			cVERT)) ;c=x
						(set! c0000 (cons  y			c0000)) ;c=y
						(set! cI025 (cons (f -0.25 x y)	cI025)) ;c=y - x/4
						(set! cD025 (cons (f +0.25 x y)	cD025)) ;c=y + x/4
						(set! cI050 (cons (f -0.50 x y)	cI050)) ;c=y -2x/4
						(set! cD050 (cons (f +0.50 x y)	cD050)) ;c=y +2x/4
						(set! cI075 (cons (f -0.75 x y)	cI075)) ;c=y -3x/4
						(set! cD075 (cons (f +0.75 x y)	cD075)) ;c=y +3x/4
						(set! cI100 (cons (f -1.00 x y)	cI100)) ;c=y -4x/4
						(set! cD100 (cons (f +1.00 x y)	cD100)) ;c=y +4x/4
						(set! cI125 (cons (f -1.25 x y)	cI125)) ;c=y -5x/4
						(set! cD125 (cons (f +1.25 x y)	cD125)) ;c=y +5x/4
						(set! cI150 (cons (f -1.50 x y)	cI150)) ;c=y -6x/4
						(set! cD150 (cons (f +1.50 x y)	cD150)) ;c=y +6x/4
						(set! count (+ count 6))
					)
				)
				(gimp-image-remove-vectors image newpath)
				(let* 
					(	;It must convert lines from Slope Intercept formula to Standard Form formula.
						;y=mx+c  ->  Ax+By=C  ::  A=-m  B=1  C=c
						(coord-lst (list
							; Front image
							(append	(i +1.00 +0.00 (apply max cVERT) +0.00 +1.00 (apply max c0000)) 	(i +1.00 +0.00 (apply min cVERT) +0.00 +1.00 (apply max c0000)) 
									(i +1.00 +0.00 (apply min cVERT) +0.00 +1.00 (apply min c0000)) 	(i +1.00 +0.00 (apply max cVERT) +0.00 +1.00 (apply min c0000))	)
							; South wall 
							(append	(i +1.00 +0.00 (apply max cVERT) -0.50 +1.00 (apply max cI050)) 	(i +1.00 +0.00 (apply min cVERT) -0.50 +1.00 (apply max cI050)) 
									(i +1.00 +0.00 (apply min cVERT) -0.50 +1.00 (apply min cI050)) 	(i +1.00 +0.00 (apply max cVERT) -0.50 +1.00 (apply min cI050))	)
							; East Wall
							(append	(i +1.00 +0.00 (apply max cVERT) +0.50 +1.00 (apply max cD050)) 	(i +1.00 +0.00 (apply min cVERT) +0.50 +1.00 (apply max cD050)) 
									(i +1.00 +0.00 (apply min cVERT) +0.50 +1.00 (apply min cD050)) 	(i +1.00 +0.00 (apply max cVERT) +0.50 +1.00 (apply min cD050))	)
							; Floor tile 
							(append	(i -0.50 +1.00 (apply max cI050) +0.50 +1.00 (apply max cD050)) 	(i -0.50 +1.00 (apply min cI050) +0.50 +1.00 (apply max cD050)) 
									(i -0.50 +1.00 (apply min cI050) +0.50 +1.00 (apply min cD050)) 	(i -0.50 +1.00 (apply max cI050) +0.50 +1.00 (apply min cD050))	)
							; South slope 25%
							(append	(i -0.50 +1.00 (apply max cI050) +0.75 +1.00 (apply max cD075)) 	(i -0.50 +1.00 (apply min cI050) +0.75 +1.00 (apply max cD075)) 
									(i -0.50 +1.00 (apply min cI050) +0.75 +1.00 (apply min cD075)) 	(i -0.50 +1.00 (apply max cI050) +0.75 +1.00 (apply min cD075))	)
							; East slope 25% 
							(append	(i +0.50 +1.00 (apply max cD050) -0.75 +1.00 (apply max cI075)) 	(i +0.50 +1.00 (apply min cD050) -0.75 +1.00 (apply max cI075)) 
									(i +0.50 +1.00 (apply min cD050) -0.75 +1.00 (apply min cI075)) 	(i +0.50 +1.00 (apply max cD050) -0.75 +1.00 (apply min cI075))	)
							; North slope 25% 
							(append	(i -0.50 +1.00 (apply max cI050) +0.25 +1.00 (apply max cD025)) 	(i -0.50 +1.00 (apply min cI050) +0.25 +1.00 (apply max cD025)) 
									(i -0.50 +1.00 (apply min cI050) +0.25 +1.00 (apply min cD025)) 	(i -0.50 +1.00 (apply max cI050) +0.25 +1.00 (apply min cD025))	)
							; West slope 25% 
							(append	(i +0.50 +1.00 (apply max cD050) -0.25 +1.00 (apply max cI025)) 	(i +0.50 +1.00 (apply min cD050) -0.25 +1.00 (apply max cI025)) 
									(i +0.50 +1.00 (apply min cD050) -0.25 +1.00 (apply min cI025)) 	(i +0.50 +1.00 (apply max cD050) -0.25 +1.00 (apply min cI025))	)
							; South slope 50% 
							(append	(i -0.50 +1.00 (apply max cI050) +1.00 +1.00 (apply max cD100)) 	(i -0.50 +1.00 (apply min cI050) +1.00 +1.00 (apply max cD100)) 
									(i -0.50 +1.00 (apply min cI050) +1.00 +1.00 (apply min cD100)) 	(i -0.50 +1.00 (apply max cI050) +1.00 +1.00 (apply min cD100))	)
							; East slope 50% 
							(append	(i +0.50 +1.00 (apply max cD050) -1.00 +1.00 (apply max cI100)) 	(i +0.50 +1.00 (apply min cD050) -1.00 +1.00 (apply max cI100)) 
									(i +0.50 +1.00 (apply min cD050) -1.00 +1.00 (apply min cI100)) 	(i +0.50 +1.00 (apply max cD050) -1.00 +1.00 (apply min cI100))	)
							; North slope 50% 
							(append	(i -0.50 +1.00 (apply max cI050) +0.00 +1.00 (apply max c0000)) 	(i -0.50 +1.00 (apply min cI050) +0.00 +1.00 (apply max c0000)) 
									(i -0.50 +1.00 (apply min cI050) +0.00 +1.00 (apply min c0000)) 	(i -0.50 +1.00 (apply max cI050) +0.00 +1.00 (apply min c0000))	)
							; West slope 50% 
							(append	(i +0.50 +1.00 (apply max cD050) +0.00 +1.00 (apply max c0000)) 	(i +0.50 +1.00 (apply min cD050) +0.00 +1.00 (apply max c0000)) 
									(i +0.50 +1.00 (apply min cD050) +0.00 +1.00 (apply min c0000)) 	(i +0.50 +1.00 (apply max cD050) +0.00 +1.00 (apply min c0000))	)
							; South slope 75% 
							(append	(i -0.50 +1.00 (apply max cI050) +1.25 +1.00 (apply max cD125)) 	(i -0.50 +1.00 (apply min cI050) +1.25 +1.00 (apply max cD125)) 
									(i -0.50 +1.00 (apply min cI050) +1.25 +1.00 (apply min cD125)) 	(i -0.50 +1.00 (apply max cI050) +1.25 +1.00 (apply min cD125))	)
							; East slope 75% 
							(append	(i +0.50 +1.00 (apply max cD050) -1.25 +1.00 (apply max cI125)) 	(i +0.50 +1.00 (apply min cD050) -1.25 +1.00 (apply max cI125)) 
									(i +0.50 +1.00 (apply min cD050) -1.25 +1.00 (apply min cI125)) 	(i +0.50 +1.00 (apply max cD050) -1.25 +1.00 (apply min cI125))	)
							; North slope 75% 
							(append	(i -0.50 +1.00 (apply max cI050) -0.25 +1.00 (apply max cI025)) 	(i -0.50 +1.00 (apply min cI050) -0.25 +1.00 (apply max cI025)) 
									(i -0.50 +1.00 (apply min cI050) -0.25 +1.00 (apply min cI025)) 	(i -0.50 +1.00 (apply max cI050) -0.25 +1.00 (apply min cI025))	)
							; West slope 75% 
							(append	(i +0.50 +1.00 (apply max cD050) +0.25 +1.00 (apply max cD025)) 	(i +0.50 +1.00 (apply min cD050) +0.25 +1.00 (apply max cD025)) 
									(i +0.50 +1.00 (apply min cD050) +0.25 +1.00 (apply min cD025)) 	(i +0.50 +1.00 (apply max cD050) +0.25 +1.00 (apply min cD025))	))
						)
						(coord-index 
							(length (cdr (member (apply min (map area coord-lst)) (reverse (map area coord-lst)))))
						)
					)
					(gimp-selection-none image)
					(gimp-image-undo-group-end image)				
					(gimp-progress-end)
					(cons coord-index (list-ref coord-lst coord-index))
				)
			) ;end of let*
		) ;end of begin
		(begin ;ELSE
			(gimp-image-undo-group-end image)				
			(gimp-message "The source image is empty or doesn't contain a valid shape.")
			(list -1 0 0 0 0 0 0 0 0)
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

(define (script-fu-simutrans-color-grid image drawable tilesize new-tilesize)
	(let* 
		(
			(width		(car (gimp-image-width  image)))
			(height		(car (gimp-image-height image)))
			(new-width	(quotient (* width new-tilesize) tilesize))
			(new-height	(quotient (* height new-tilesize) tilesize))
			(x			width)
			(y			height)
		)
		
		(gimp-image-undo-group-start image)		
		
		(gimp-image-resize image new-width new-height 0 0)
		(for-each-layer image (string)
			(lambda (this-layer) (gimp-layer-resize-to-image-size this-layer))
		)
		(while (> y 0)
			(set! y (- y tilesize))
			(set! x width)
			(while (> x 0)
				(set! x (- x tilesize))
				(gimp-rect-select image x y tilesize tilesize CHANNEL-OP-REPLACE FALSE 0)
				; (selection-fill drawable (list (quotient (* x 255) width) (quotient (* y 255) height) 127))
				; (quotient (* width new-tilesize) tilesize)
				(script-fu-simutrans-move-multilayer image drawable (* x (/ (- new-tilesize tilesize) tilesize)) (* y (/ (- new-tilesize tilesize) tilesize)) (string) FALSE)
			)
		)
		
		(gimp-image-grid-set-spacing image new-tilesize new-tilesize)
		(gimp-image-grid-set-offset image 0 0)			
		
		(gimp-image-undo-group-end image)
		(gimp-progress-end)
		(gimp-displays-flush)
	)
)

(define (script-fu-simutrans-isometric-surface image drawable inclination orientation interpolation)
	(gimp-image-undo-group-start image)
	(unless (= (car (gimp-image-get-floating-sel image)) drawable) 
		(unless (zero? (car (gimp-selection-is-empty image))) (gimp-selection-all image))
		(set! drawable (car (gimp-selection-float drawable 0 0)))
	)
	(let* 
		(
			(width	(car (gimp-drawable-width  drawable))) 
			(height	(car (gimp-drawable-height drawable)))
			;Southern orientation
			(xs3		(+ (list-ref (gimp-drawable-offsets drawable) 0) width	-0.5 ))
			(ys3		(+ (list-ref (gimp-drawable-offsets drawable) 1) height  	 ))
			(xs2		(- xs3 		width 		(if (<= 	inclination	-3)	2 0) 	 ))
			(ys2		(- ys3	(/	width	2)	(if (<= -2	inclination	+0)	0 1) 	 ))
			(xs1		(+ xs3 		height 		(if (<= 	inclination	-2)	2 0) 	 ))
			(ys1		(- ys3 	(* 	height		(/	(+	2 	inclination) 4)	   ) 	 )) 
			(xs0		(- xs1 		width 		(if (<= 	inclination	-3)	2 0) 	 ))
			(ys0		(- ys1 	(/ 	width	2) 	(if (<= 	inclination	+0)	0 1) 	 ))
			;Eastern orientation
			(xe2		(+ (list-ref (gimp-drawable-offsets drawable) 0) 		-0.5 ))
			(ye2		(+ (list-ref (gimp-drawable-offsets drawable) 1) height		 ))
			(xe3		(+ xe2 		width 		(if (<= 	inclination	-2)	2 0) 	 ))
			(ye3		(- ye2	(/	width	2)	(if (<= 	inclination	-2)	1 0) 	 ))
			(xe0		(- xe2 		height 		(if (<= 	inclination	-2)	2 0) 	 ))
			(ye0		(- ye2 	(* 	height		(/	(+	2	inclination) 4)	   ) 	 ))
			(xe1		(+ xe0 		width 		(if (<= 	inclination	-2)	2 0) 	 ))
			(ye1		(- ye0 	(/ 	width	2) 	(if (<= 	inclination	-2)	1 0) 	 ))
		)
		(gimp-drawable-transform-perspective drawable 
			(if (zero? orientation) xs0 xe0)	(if (zero? orientation) ys0 ye0)
			(if (zero? orientation) xs1 xe1)	(if (zero? orientation) ys1 ye1)
			(if (zero? orientation) xs2 xe2)	(if (zero? orientation) ys2 ye2)
			(if (zero? orientation) xs3 xe3)	(if (zero? orientation) ys3 ye3)
			TRANSFORM-FORWARD interpolation FALSE 3 TRANSFORM-RESIZE-ADJUST
		)
		(plug-in-autocrop-layer RUN-NONINTERACTIVE image drawable)
	)
	(gimp-image-undo-group-end image)	
	(gimp-progress-end)
	(gimp-displays-flush)
)

(define (script-fu-simutrans-isometric-surface-reverse image drawable interpolation)
	(gimp-image-undo-group-start image)
	(unless (= (car (gimp-image-get-floating-sel image)) drawable) 
		(unless (zero? (car (gimp-selection-is-empty image))) (gimp-selection-all image))
		(set! drawable (car (gimp-selection-float drawable 0 0)))
	)
	(let* 
		(
			(width	(car (gimp-drawable-width  drawable))) 
			(height	(car (gimp-drawable-height drawable)))
			(coord	(cdr (detect-shape image drawable)))
		)
		(gimp-drawable-transform-perspective drawable 
			(list-ref coord 4)	(list-ref coord 5)
			(list-ref coord 2)	(list-ref coord 3)
			(list-ref coord 6)	(list-ref coord 7)
			(list-ref coord 0)	(list-ref coord 1)
			TRANSFORM-BACKWARD interpolation FALSE 3 TRANSFORM-RESIZE-ADJUST
		)
		(plug-in-autocrop-layer RUN-NONINTERACTIVE image drawable)
	)
	(gimp-image-undo-group-end image)	
	(gimp-progress-end)
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

(define (script-fu-simutrans-slope-shading image drawable autodetect inclination orientation)
	(gimp-image-undo-group-start image)
	(gimp-context-push)
	
	;There must be a selection to be cut into temp buffer
	(unless (zero? (car (gimp-selection-is-empty image))) (gimp-selection-all image))
	
	;Cut selection, edit in temp image, and paste back
	(let ((temp-buffer-name	(car (gimp-edit-named-cut drawable "simutrans-shade-slope"))))
		(edit-buffer-in-temporary-image temp-buffer-name 
			(lambda (temp-image temp-layer)
				(let* 
					(
						(index
							(max 3 ;Flat Tile of Slope
								(if	(zero? autodetect)
									(+ (* 4 (abs inclination)) (if (negative? inclination) 2 0) orientation)
									(car (detect-shape temp-image temp-layer))
								)
							)
						)
						(mask (car (gimp-layer-new temp-image 1 1 RGBA-IMAGE temp-buffer-name 100 GRAIN-MERGE-MODE)))
					)	
					(gimp-image-add-layer temp-image mask -1)
					(gimp-layer-resize-to-image-size mask)
					(gimp-context-set-foreground (list-ref slope-shading-colors-list index))
					(gimp-drawable-fill mask FOREGROUND-FILL)
					(set! temp-layer (car (gimp-image-merge-visible-layers temp-image CLIP-TO-IMAGE)))
				) ;end of let
			) ;end of lambda (temp-image temp-layer)
		)			
		(gimp-floating-sel-anchor (car (gimp-edit-named-paste drawable temp-buffer-name 1)))
		(gimp-buffer-delete temp-buffer-name)			
	)

	(gimp-context-pop)
	(gimp-image-undo-group-end image)
	(gimp-progress-end)
	(gimp-displays-flush)
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
	"Create Isometric _Surface..."
	"Create an isometric surface..."
	"Fabio Gonella"
	"Fabio Gonella"
	"July 2013"
	""
	SF-IMAGE	 	"Image"	   					0
	SF-DRAWABLE  	"Drawable"					0
	SF-ADJUSTMENT	(string-append 
						"Destination _Inclination" 
						(string #\newline) 
						"( 0 = Flat tile )"
					)							'(+0 -3 +3 1 1 0 0)
	SF-ADJUSTMENT	(string-append 
						"Destination _Orientation" 
						(string #\newline) 
						"( 0 = South, " 
						;(string #\newline) 
						"1 = East )"
					)							'(+0 +0 +1 1 1 0 0)
	SF-ENUM 		"Inter_polation" 			'("InterpolationType" "none")
)

(script-fu-register "script-fu-simutrans-slope-shading"
	"S_lope shading..."
	"Apply default shading to a slope..."
	"Fabio Gonella"
	"Fabio Gonella"
	"September 2013"
	""
	SF-IMAGE	 	"Image"	   					0
	SF-DRAWABLE  	"Drawable"					0
	SF-TOGGLE	 	"_Autodetect slope inclination & orientation" TRUE
	SF-ADJUSTMENT	(string-append 
						"Slope _Inclination" 
						(string #\newline) 
						"( 0 = Flat tile )"
					)							'(+0 -2 +2 1 1 0 0)
	SF-ADJUSTMENT	(string-append 
						"Slope _Orientation" 
						(string #\newline) 
						"( 0 = South, " 
						;(string #\newline) 
						"1 = East )"
					)							'(+0 +0 +1 1 1 0 0)
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
	"<Image>/Si_mutrans/_Color Tools" )

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
	"<Image>/Si_mutrans/_Isometric Tools" )

(script-fu-menu-register "script-fu-simutrans-slope-shading"
	"<Image>/Si_mutrans/_Isometric Tools" )
	
(script-fu-menu-register "script-fu-swap-colors"
	"<Image>/Si_mutrans/_Color Tools" )	
	
(script-fu-menu-register "script-fu-simutrans-set-grid"
    "<Image>/Si_mutrans/Image Tools" )	
	
