

#include <png.h>
#include <setjmp.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>


/*
 * Hajo: RGB 888 padded to 32 bit
 */
typedef unsigned long   PIXRGB;

/* special colors */
#define TRANSPARENT ((0x00E7l<<8)|(0x00FFl<<16)|(0x00FF<<24l))



PIXRGB default_color = 0x80808000;


int correct_color( int input )
{
	// first clipt the color
	if(  input<1  ) {
		return 1;
	}
	if(  input>254  ) {
		return 254;
	}
	// then avoid special greys
	switch( input )
	{
		case 0x6B :	return input-1;
		case 0x9B :
		case 0xB3 :
		case 0xC9 :
		case 0xDF : return input+1;
		default :   return input;
	}
}


// output either a 32 or 16 or 15 bitmap
int write_png( const char *file_name, unsigned char *data, int width, int height, int bit_depth )
{
	png_structp png_ptr = NULL;
	png_infop info_ptr = NULL;
	FILE *fp = fopen(file_name, "wb");
	if (!fp) {
		return 0;
	}

	// init structures
	png_ptr = png_create_write_struct(PNG_LIBPNG_VER_STRING, NULL, NULL, NULL);
	if (!png_ptr) {
		fclose( fp );
		return 0;
	}

	info_ptr = png_create_info_struct(png_ptr);
	if (!info_ptr) {
		png_destroy_write_struct( &png_ptr, (png_infopp)NULL );
		fclose( fp );
		return 0;
	}

#ifdef PNG_SETJMP_SUPPORTED
	if(  setjmp( png_jmpbuf(png_ptr) )  ) {
		printf("write_png: fatal error.\n");
		png_destroy_write_struct(&png_ptr, &info_ptr);
		exit(1);
	}
#endif

	// assign file
	png_init_io(png_ptr, fp);

#if PNG_LIBPNG_VER_MAJOR<=1  &&  PNG_LIBPNG_VER_MINOR<5
	/* set the zlib compression level */
	png_set_compression_level( png_ptr, Z_BEST_COMPRESSION );
#endif

	// output header
	png_set_IHDR( png_ptr, info_ptr, width, height, 8, PNG_COLOR_TYPE_RGB, PNG_INTERLACE_NONE, PNG_INTERLACE_NONE, PNG_FILTER_TYPE_DEFAULT );
	png_write_info(png_ptr, info_ptr);

	if(  bit_depth==32  ) {
		// write image data
		int i;
		png_set_filler(png_ptr, 0, PNG_FILLER_BEFORE);
		for(  i=0;  i<height;  i++ ) {
			png_bytep row_pointer = data+(i*width*4);
			png_write_row( png_ptr, row_pointer );
		}
	}
	else {
		puts("No implemented yet!\n");
		exit(0);
	}

	// free all
	png_write_end(png_ptr, info_ptr);
	png_destroy_write_struct(&png_ptr, &info_ptr);

	fclose( fp );
	return 1;
}


int pak = 64;
int slope_step = 16;

bool make_lightmap = false;
bool make_marker = false;

double sun[3]={ -1, 0, 1 }, sun_abs=1;

// how much change of brightness
int brightness = 32;
int base_brightness = 128;

#define corner_sw(i) (i%3)    	// sw corner
#define corner_se(i) ((i/3)%3)	// se corner
#define corner_ne(i) ((i/9)%3)	// ne corner
#define corner_nw(i) (i/27)   	// nw corner

#define einfach (0)
#define wegbar_ns (1)
#define wegbar_ow (1)
#define frontback (2) /* need to divide left right */
#define illegal (4)

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
const int hang[81] = {
	wegbar_ns | wegbar_ow, // 0:flach                 // flat			ns|ew          
	frontback,                                                // sw1                                  
	frontback,                                                // sw2                                  
	frontback,                                                // se1                                  
	wegbar_ns | einfach,   // 4:nordhang              // se1,sw1		ns             
	0,                                                // se1,sw2		               
	0,                                                // se2                                  
	0,                                                // se2,sw1                              
	wegbar_ns | einfach,   // 8: double height north  // se2,sw2		ns2            
	frontback,                                                // ne1                                  
	frontback,                                                // ne1,sw1                              
	frontback,                                                // ne1,sw2                              
	wegbar_ow | einfach,   // 12:westhang             // ne1,se1		ew             
	0,                                                // ne1,se1,sw1                          
	frontback,                                                // ne1,se1,sw2                          
	0,                                                // ne1,se2                              
	0,                                                // ne1,se2,sw1                          
	0,                                                // ne1,se2,sw2                          
	frontback,                                                // ne2                                  
	frontback,                                                // ne2,sw1                              
	frontback,                                                // ne2,sw2                              
	frontback,                                                // ne2,se1                              
	frontback,                                                // ne2,se1,sw1		               
	frontback,                                                // ne2,se1,sw2		               
	wegbar_ow | einfach,   // 24: double height west  // ne2,se2		ew2            
	frontback,                                                // ne2,se2,sw1                          
	frontback,                                                // ne2,se2,sw2                          
	0,                                                // nw1                                  
	wegbar_ow | einfach,   // 28:osthang              // nw1,sw1		ew             
	frontback,                                                // nw1,sw2		               
	0,                                                // nw1,se1                              
	0,                                                // nw1,se1,sw1                          
	frontback,                                                // nw1,se1,sw2                          
	0,                                                // nw1,se2                              
	0,                                                // nw1,se2,sw1		               
	0,                                                // nw1,se2,sw2		               
	wegbar_ns | einfach,   // 36:suedhang             // nw1,ne1		ns             
	frontback,                                                // nw1,ne1,sw1                          
	frontback,                                                // nw1,ne1,sw2		               
	0,                                                // nw1,ne1,se1                          
	wegbar_ns | wegbar_ow | illegal, // 40:all 1 tile // nw1,ne1,se1,sw1	TODO	0 up 1 high
	illegal,                                          // nw1,ne1,se1,sw2	TODO	1 up 1 
	0,                                                // nw1,ne1,se2                          
	illegal,                                          // nw1,ne1,se2,sw1	TODO	3 up 1 
	wegbar_ns | einfach | illegal,   // 44 nordhang 2 // nw1,ne1,se2,sw2	TODOns	4 up 1 
	frontback,                                        // nw1,ne2                              
	frontback,                                        // nw1,ne2,sw1                          
	frontback,                                        // nw1,ne2,sw2		               
	frontback,                                        // nw1,ne2,se1		               
	illegal,                                          // nw1,ne2,se1,sw1	TODO	9 up 1 
	illegal,                                          // nw1,ne2,se1,sw2	TODO	10 up 1
	0,                                                // nw1,ne2,se2                          
	wegbar_ow | einfach | illegal,   // 52 westhang 2 // nw1,ne2,se2,sw1	TODOew	12 up 1
	illegal,                                          // nw1,ne2,se2,sw2	TODO	13 up 1
	0,                                                // nw2                                  
	0,                                                // nw2,sw1		               
	wegbar_ow | einfach,   // 56:double height east   // nw2,sw2		ew2            
	0,                                                // nw2,se1                              
	0,                                                // nw2,se1,sw1		               
	0,                                                // nw2,se1,sw2                          
	0,                                                // nw2,se2                              
	0,                                                // nw2,se2,sw1		               
	0,                                                // nw2,se2,sw2                          
	0,                                                // nw2,ne1		               
	0,                                                // nw2,ne1,sw1                          
	frontback,                                        // nw2,ne1,sw2                          
	0,                                                // nw2,ne1,se1		               
	illegal,                                          // nw2,ne1,se1,sw1	TODO	27 up 1
	wegbar_ow | einfach | illegal,   // 68:osthang 2  // nw2,ne1,se1,sw2	TODOew	28 up 1
	0,                                                // nw2,ne1,se2		               
	illegal,                                          // nw2,ne1,se2,sw1	TODO	30 up 1
	illegal,                                          // nw2,ne1,se2,sw2	TODO	31 up 1
	wegbar_ns | einfach,   // 72:double height south  // nw2,ne2		ns2            
	frontback,                                        // nw2,ne2,sw1                          
	frontback,                                        // nw2,ne2,sw2                          
	frontback,                                        // nw2,ne2,se1		               
	wegbar_ns | einfach | illegal,   // 76:suedhang 2 // nw2,ne2,se1,sw1	TODOns	36 up 1
	illegal,                                          // nw2,ne2,se1,sw2	TODO	37 up 1
	0,                                                // nw2,ne2,se2                          
	illegal,                                          // nw2,ne2,se2,sw1	TODO	39 up 1
	wegbar_ns | wegbar_ow | illegal  // 80:all 2 tile // nw2,ne2,se2,sw2	TODO	0 up 2 high
};


void DrawVerticalGray( PIXRGB *dest, int h, int dest_w, long grey )
{
	long color = (grey<<16) | (grey<<8) | (grey<<24l);
	while(  h-->0  ) {
		*dest = color;
		dest += dest_w;
	}
}


void DrawVerticalColor( PIXRGB *dest, int h, int dest_w, long color )
{
	while(  h-->0  ) {
		*dest = color;
		dest += dest_w;
	}
}


void DrawLine( int x0, int y0, const int x1, const int y1, int *y_coord, bool upper )
{
	int dx = abs(x1-x0);
	int dy = abs(y1-y0);
	int sx = ( x0 < x1 ) ? 1 : -1;
	int sy = ( y0 < y1 ) ? 1 : -1;
	int err = dx-dy;
 
	int last_x0 = -1;
	while(1) {
		if(  last_x0 != x0  ||  (upper ^ (y_coord[x0] <= y0) )  ) {
			y_coord[x0] = y0;
			last_x0 = x0;
		}
		if(  x0 == x1  &&  y0 == y1  ) {
			break;
		}
		int e2 = 2*err;
		if(  e2 > -dy  ) {
	       err = err - dy;
	       x0 = x0 + sx;
		}
		if(  x0 == x1  &&  y0 == y1  ) {
			if(  last_x0 != x0  ||  (upper ^ (y_coord[x0] <= y0) )  ) {
				y_coord[x0] = y0;
			}
			break;
		}
		if(  e2 <  dx  ) {
			err = err + dx;
			y0 = y0 + sy;
		}
	}
}


void CreateSlope( int slope, PIXRGB *dest, long w )
{
	// just for less typing ...
	const int sw = corner_sw(slope); 
	const int se = corner_se(slope); 
	const int ne = corner_ne(slope); 
	const int nw = corner_nw(slope); 

	/* first we get the tile corners y-coordinates */
	int corner[4];
	corner[0] = pak/4-1+slope_step*sw;	// sw (left)
	corner[1] = slope_step*se;			// se (front)
	corner[2] = pak/4-1+slope_step*ne;	// ne (right)
	corner[3] = pak/2-2+slope_step*nw;	// nw (back)
	// and now from the bottom
	for(  int i=0;  i<4;  i++ ) {
		corner[i] = pak-corner[i]-1;
	}

	if(  corner[1] == corner[3]  ||  (hang[slope] & illegal)  ) {
		// just a pixel, otherwise empty
		DrawVerticalGray( dest+corner[0]*w, 1, w, 128 );
		return;
	}

	/* now we can built the two triangles using two line algorithm ...
	 * first the top line then then bottom
	 */
	int upper_line[1024];
	DrawLine( 0, corner[0], pak/2-1, corner[3], upper_line, false );
	DrawLine( pak/2, corner[3], pak-1, corner[2], upper_line, false );

	int bottom_line[1024];
	DrawLine( 0, corner[0], pak/2-1, corner[1], bottom_line, false );
	DrawLine( pak/2, corner[1], pak-1, corner[2], bottom_line, false );

	int diagonal = hang[slope]&frontback;
	// now, back may be lower than the diagonal
	if(  corner[3] > (corner[0]+corner[2])/2  ) {
		corner[3] = (corner[0]+corner[2])/2;
		diagonal = true;
	}

	/* we have a triangle with four corners. In order to give a slope angle of roughly 22.5 for 16 slope_step
	 * we have to have tan(22.5)=16/x => x=38,627...
	 * However, 1 is easier to calculate, so we make those vectors just shorter in z direction
	 * SE =: a at (0,0,se*slope_step/38)
	 * SW =: b at (0,1,sw*slope_step/38)
	 * NW =: c at (1,1,nw*slope_step/38)
	 * NE =: d at (1,0,ne*slope_step/38)
	 */
	const double z_step = slope_step/38.627;

	// either the tile is left right slopes or front back. first comes left-right ones
	if(  abs(nw-se) >= abs(sw-ne) ) {
		/* Then we get left and right normal with the above definintion
		 * left (SW corner normal): (b-c) x (b-a) = (nw-sw,se-sw,-1)
		 * right (NE corner normal): (d-a) x (d-c) = (ne-se,ne-nw,-1)
		 *
		 * The sun is in the south at 45 deg, i.e. at s=( 0, -1, 1 )
		 * (we only care about angle, so we forget about the se offset ...)
		 * angle to the sun is now cos(i,s) = l.s/(|l|*|s|) (and the same for r)
		 * |s|=sqrt(2) and for the rest we use floating point
		 * 
		 * And nature is nice, so the diffuse reflected light (aka brightness) is the cos of the angle ...
		 */
		int left_brigthnes = correct_color( base_brightness - (int)( (brightness*( (nw-sw)*sun[0]*z_step + (se-sw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (nw-sw)*(nw-sw)*z_step*z_step + (se-sw)*(se-sw)*z_step*z_step + 1.0 ) ) ) );
		int right_brigthnes = correct_color( base_brightness - (int)( (brightness*( (ne-se)*sun[0]*z_step + (ne-nw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (ne-se)*(ne-se)*z_step*z_step + (ne-nw)*(ne-nw)*z_step*z_step + 1.0 ) ) ) );

		// nor we can render the tile
		for(  int x=0;  x<pak;  x++  ) {
			DrawVerticalGray( dest+x+upper_line[x]*w, bottom_line[x]-upper_line[x]+1, w, x<=pak/2-1 ? left_brigthnes : right_brigthnes );
		}
	}
	else {
		/* Then we get left and right normal with the above definintion
		 * back (NW corner normal): (c-d) x (c-b) = (nw-sw,ne-nw,-1)
		 * front (SE corner normal): (a-b) x (a-d) = (ne-se,se-sw,-1)
		 */
		int back_brigthnes = correct_color( base_brightness - (int)( (brightness*( (nw-sw)*sun[0]*z_step + (ne-nw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (nw-sw)*(nw-sw)*z_step*z_step + (ne-nw)*(ne-nw)*z_step*z_step + 1.0 ) ) ) );
		int front_brigthnes = correct_color( base_brightness - (int)( (brightness*( (ne-se)*sun[0]*z_step + (se-sw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (ne-se)*(ne-se)*z_step*z_step + (se-sw)*(se-sw)*z_step*z_step + 1.0 ) ) ) );

		/* now we can built the two triangles using two line algorithm ...
		 * first the middle line then then bottom
		 */
		int middle_line[1024];
		DrawLine( 0, corner[0], pak-1, corner[2], middle_line, false );

		// nor we can render the front tile
		for(  int x=0;  x<pak;  x++  ) {
			DrawVerticalGray( dest+x+middle_line[x]*w, bottom_line[x]-middle_line[x]+1, w, front_brigthnes );
			DrawVerticalGray( dest+x+upper_line[x]*w, middle_line[x]-upper_line[x]+1, w, back_brigthnes );
		}
	}
}


void CreateMarker( int slope, PIXRGB *dest, long w )
{
	// just for less typing ...
	int sw = corner_sw(slope); 
	int se = corner_se(slope); 
	int ne = corner_ne(slope); 
	int nw = corner_nw(slope); 

	if(  slope >= 27  ) {
		// front slope
		nw = ne;
		ne = se; 
	}

	/* first we get the tile corners y-coordinates */
	int corner[4];
	corner[0] = (pak/4-1)+slope_step*sw;	// sw (left)
	corner[1] = slope_step*se;			// se (front)
	corner[2] = (pak/4-1)+slope_step*ne;	// ne (right)
	corner[3] = (pak/2-2)+slope_step*nw;	// nw (back)
	// and now from the bottom
	for(  int i=0;  i<4;  i++ ) {
		corner[i] = pak-corner[i]-1;
	}

	int line[1024];
	if(  slope < 27  ) {
		// font part
		DrawLine( 0, corner[0], pak/2-1, corner[1], line, false );
		DrawLine( pak/2, corner[1], pak-1, corner[2], line, false );
	}
	else {
		DrawLine( 0, corner[0], pak/2-1, corner[3], line, false );
		DrawLine( pak/2, corner[3], pak-1, corner[2], line, false );
	}

	// now actually draw something
	for(  int x=0;  x<pak;  x++  ) {
		DrawVerticalColor( dest+x+line[x]*w, 1, w, default_color );
	}
}


void Usage(char *programName)
{
	fprintf(stderr,"%s usage:\n-pak64 -slope16 -bright32 Target.png\n-pak64 -marker16 Target.png\ngenerates marker or a lightmap for the sun in south",programName);
	/* Modify here to add your usage message when the program is
	 * called without arguments */
}



/* returns the index of the first argument that is not an option; i.e.
   does not start with a dash or a slash
*/
int HandleOptions(int argc,char *argv[])
{
	int i,firstnonoption=0;

	for (i=1; i< argc;i++) {
		if (argv[i][0] == '/' || argv[i][0] == '-') {
			switch (argv[i][1]) {
				/* An argument -? means help is requested */
				case '?':
					Usage(argv[0]);
					break;

				case 'h':
				case 'H':
					Usage(argv[0]);
					break;

				case 'c':
				case 'C':
					if(  !stricmp(argv[i]+1,"c#")  ) {
						Usage(argv[0]);
						break;
					}
					default_color = strtol( argv[i]+3, NULL, 16 );
					default_color = (((default_color>>16)&0x000000FF)<<8)|(((default_color>>8)&0x000000FF)<<16)|((default_color&0x000000FF)<<24l);
					break;


				case 'p':
				case 'P':
					if(  !stricmp(argv[i]+1,"pak")  ) {
						Usage(argv[0]);
						break;
					}
					pak = atol( argv[i]+4 );
					break;
					
				case 's':
				case 'S':
					if(  !stricmp(argv[i]+1,"slope")  ) {
						Usage(argv[0]);
						break;
					}
					make_lightmap = true;
					slope_step = atol( argv[i]+6 );
					break;

				case 'm':
				case 'M':
					if(  !stricmp(argv[i]+1,"marker")  ) {
						Usage(argv[0]);
						break;
					}
					make_marker = true;
					slope_step = atol( argv[i]+7 );
					break;

				case 'b':
				case 'B':
					if(  !stricmp(argv[i]+1,"bright")  ) {
						Usage(argv[0]);
						break;
					}
					brightness = atol( argv[i]+7 );
					break;

				/* add your option switches here */
				default:
					fprintf(stderr,"unknown option %s\n",argv[i]);
					break;
			}
		}
		else {
			firstnonoption = i;
			break;
		}
	}
	return firstnonoption;
}



int main(int argc,char *argv[])
{
	int i;
	FILE *fIn;
	if(argc == 1) {
		/* If no arguments we call the Usage routine and exit */
		Usage(argv[0]);
		return 1;
	}
	/* handle the program options */
	int last_option = HandleOptions(argc,argv);
	if(  (make_lightmap ^ make_marker) == 0  ) {
		// can only generate either lightmap or marker
		Usage(argv[0]);
		return 1;
	}

	/* init bitmap */
	const long row = make_lightmap ? 16 : 9;
	const long bitmap_x = pak*row;
	const long bitmap_y = ((80/row)+1)*pak;
	PIXRGB *bitmap = (PIXRGB *)malloc(bitmap_x*bitmap_y*sizeof(PIXRGB));
	for(  int i=0;  i<=bitmap_x*bitmap_y;  i++  ) {
		bitmap[i] = TRANSPARENT;
	}

	if(  make_lightmap  ) {
		/* Create tile by tile */
		sun_abs = sqrt( sun[0]*sun[0] + sun[1]*sun[1] + sun[2]*sun[2] );
		base_brightness = 128 - (int)(brightness*sun[2]/(sun_abs+1.0));
		for(  i=0;  i<=80;  i++  ) {
			CreateSlope( i, bitmap + pak*(i%row) + (i / row)*bitmap_x*pak, bitmap_x );
		}
	}
	else {
		for(  i=0;  i<54;  i++  ) {
			CreateMarker( i, bitmap + pak*(i%row) + (i / row)*bitmap_x*pak, bitmap_x );
		}
	}

	/* write png */
	write_png( argv[last_option], (unsigned char *)bitmap, bitmap_x, bitmap_y, 32 );
	return 0;
}

