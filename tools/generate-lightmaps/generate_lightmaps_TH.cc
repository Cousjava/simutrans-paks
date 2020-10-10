

#include <png.h>
#include <setjmp.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>


/*
 * Hajo: RGB 888 padded to 32 bit
 */
typedef png_uint_32   PIXRGB;

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

#define corner_sw(i) (i%4)    	// sw corner
#define corner_se(i) ((i/4)%4)	// se corner
#define corner_ne(i) ((i/16)%4)	// ne corner
#define corner_nw(i) (i/64)   	// nw corner

#define einfach (0)
#define way_ns (1)
#define way_ew (1)
#define frontback (2) /* need to divide left right */
#define illegal (4)

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
const int hang[256] = {
	way_ns | way_ew,	// slope 0 # flat	straight ns|ew
	0,	// slope 1 # sw1
	0,	// slope 2 # sw2
	0,	// slope 3 # sw3
	0,	// slope 4 # se1
	way_ns,	// slope 5 # se1,sw1	straight ns
	0,	// slope 6 # se1,sw2
	0,	// slope 7 # se1,sw3
	0,	// slope 8 # se2
	0,	// slope 9 # se2,sw1
	way_ns,	// slope 10 # se2,sw2	straight ns2
	0,	// slope 11 # se2,sw3
	0,	// slope 12 # se3
	0,	// slope 13 # se3,sw1
	0,	// slope 14 # se3,sw2	
	way_ns,	// slope 15 # se3,sw3	straight ns3
	0,	// slope 16 # ne1
	0,	// slope 17 # ne1,sw1
	0,	// slope 18 # ne1,sw2
	0,	// slope 19 # ne1,sw3
	way_ew,	// slope 20 # ne1,se1	straight ew
	0,	// slope 21 # ne1,se1,sw1
	0,	// slope 22 # ne1,se1,sw2
	0,	// slope 23 # ne1,se1,sw3
	0,	// slope 24 # ne1,se2
	0,	// slope 25 # ne1,se2,sw1
	0,	// slope 26 # ne1,se2,sw2
	0,	// slope 27 # ne1,se2,sw3
	0,	// slope 28 # ne1,se3
	0,	// slope 29 # ne1,se3,sw1
	0,	// slope 30 # ne1,se3,sw2
	0,	// slope 31 # ne1,se3,sw3
	0,	// slope 32 # ne2
	0,	// slope 33 # ne2,sw1
	0,	// slope 34 # ne2,sw2
	0,	// slope 35 # ne2,sw3
	0,	// slope 36 # ne2,se1
	0,	// slope 37 # ne2,se1,sw1
	0,	// slope 38 # ne2,se1,sw2
	0,	// slope 39 # ne2,se1,sw3
	way_ew,	// slope 40 # ne2,se2	straight ew2
	0,	// slope 41 # ne2,se2,sw1
	0,	// slope 42 # ne2,se2,sw2
	0,	// slope 43 # ne2,se2,sw3
	0,	// slope 44 # ne2,se3
	0,	// slope 45 # ne2,se3,sw1
	0,	// slope 46 # ne2,se3,sw2
	0,	// slope 47 # ne2,se3,sw3
	0,	// slope 48 # ne3
	0,	// slope 49 # ne3,sw1
	0,	// slope 50 # ne3,sw2
	0,	// slope 51 # ne3,sw3
	0,	// slope 52 # ne3,se1
	0,	// slope 53 # ne3,se1,sw1
	0,	// slope 54 # ne3,se1,sw2
	0,	// slope 55 # ne3,se1,sw3
	0,	// slope 56 # ne3,se2
	0,	// slope 57 # ne3,se2,sw1
	0,	// slope 58 # ne3,se2,sw2
	0,	// slope 59 # ne3,se2,sw3
	way_ew,	// slope 60 # ne3,se3	straight ew3
	0,	// slope 61 # ne3,se3,sw1
	0,	// slope 62 # ne3,se3,sw2
	0,	// slope 63 # ne3,se3,sw3
	0,	// slope 64 # nw1
	way_ew,	// slope 65 # nw1,sw1	straight ew
	0,	// slope 66 # nw1,sw2
	0,	// slope 67 # nw1,sw3
	0,	// slope 68 # nw1,se1
	0,	// slope 69 # nw1,se1,sw1
	0,	// slope 70 # nw1,se1,sw2
	0,	// slope 71 # nw1,se1,sw3
	0,	// slope 72 # nw1,se2
	0,	// slope 73 # nw1,se2,sw1
	0,	// slope 74 # nw1,se2,sw2
	0,	// slope 75 # nw1,se2,sw3
	0,	// slope 76 # nw1,se3
	0,	// slope 77 # nw1,se3,sw1
	0,	// slope 78 # nw1,se3,sw2
	0,	// slope 79 # nw1,se3,sw3
	way_ns,	// slope 80 # nw1,ne1	straight ns
	0,	// slope 81 # nw1,ne1,sw1
	0,	// slope 82 # nw1,ne1,sw2
	0,	// slope 83 # nw1,ne1,sw3
	0,	// slope 84 # nw1,ne1,se1
	way_ns | way_ew | illegal,	// slope 85 # nw1,ne1,se1,sw1	TODO	0 up 1
	0 | illegal,	// slope 86 # nw1,ne1,se1,sw2	TODO	1 up 1
	0 | illegal,	// slope 87 # nw1,ne1,se1,sw3	TODO	2 up 1
	0,	// slope 88 # nw1,ne1,se2
	0 | illegal,	// slope 89 # nw1,ne1,se2,sw1	TODO	4 up 1
	way_ns | illegal,	// slope 90 # nw1,ne1,se2,sw2	TODOns	5 up 1
	0 | illegal,	// slope 91 # nw1,ne1,se2,sw3	TODOns	6 up 1
	0,	// slope 92 # nw1,ne1,se3
	0 | illegal,	// slope 93 # nw1,ne1,se3,sw1	TODO	8 up 1
	0 | illegal,	// slope 94 # nw1,ne1,se3,sw2	TODOns	9 up 1
	way_ns | illegal,	// slope 95 # nw1,ne1,se3,sw3	TODOns	10 up 1
	0,	// slope 96 # nw1,ne2
	0,	// slope 97 # nw1,ne2,sw1
	0,	// slope 98 # nw1,ne2,sw2
	0,	// slope 99 # nw1,ne2,sw3
	0,	// slope 100 # nw1,ne2,se1
	0 | illegal,	// slope 101 # nw1,ne2,se1,sw1	TODO	16 up 1
	0 | illegal,	// slope 102 # nw1,ne2,se1,sw2	TODO	17 up 1
	0 | illegal,	// slope 103 # nw1,ne2,se1,sw3	TODO	18 up 1
	0,	// slope 104 # nw1,ne2,se2
	way_ew | illegal,	// slope 105 # nw1,ne2,se2,sw1	TODOew	20 up 1
	0 | illegal,	// slope 106 # nw1,ne2,se2,sw2	TODO	21 up 1
	0 | illegal,	// slope 107 # nw1,ne2,se2,sw3	TODO	22 up 1
	0,	// slope 108 # nw1,ne2,se3
	0 | illegal,	// slope 109 # nw1,ne2,se3,sw1	TODOew	24 up 1
	0 | illegal,	// slope 110 # nw1,ne2,se3,sw2	TODO	25 up 1
	0 | illegal,	// slope 111 # nw1,ne2,se3,sw3	TODO	26 up 1
	0,	// slope 112 # nw1,ne3
	0,	// slope 113 # nw1,ne3,sw1
	0,	// slope 114 # nw1,ne3,sw2
	0,	// slope 115 # nw1,ne3,sw3
	0,	// slope 116 # nw1,ne3,se1
	0 | illegal,	// slope 117 # nw1,ne3,se1,sw1	TODO	32 up 1
	0 | illegal,	// slope 118 # nw1,ne3,se1,sw2	TODO	33 up 1
	0 | illegal,	// slope 119 # nw1,ne3,se1,sw3	TODO	34 up 1
	0,	// slope 120 # nw1,ne3,se2
	0 | illegal,	// slope 121 # nw1,ne3,se2,sw1	TODOew	36 up 1
	0 | illegal,	// slope 122 # nw1,ne3,se2,sw2	TODO	37 up 1
	0 | illegal,	// slope 123 # nw1,ne3,se2,sw3	TODO	38 up 1
	0,	// slope 124 # nw1,ne3,se3
	way_ew | illegal,	// slope 125 # nw1,ne3,se3,sw1	TODOew	40 up 1
	0 | illegal,	// slope 126 # nw1,ne3,se3,sw2	TODO	41 up 1
	0 | illegal,	// slope 127 # nw1,ne3,se3,sw3	TODO	42 up 1
	0,	// slope 128 # nw2
	0,	// slope 129 # nw2,sw1
	way_ew,	// slope 130 # nw2,sw2	straight ew2
	0,	// slope 131 # nw2,sw3
	0,	// slope 132 # nw2,se1
	0,	// slope 133 # nw2,se1,sw1
	0,	// slope 134 # nw2,se1,sw2
	0,	// slope 135 # nw2,se1,sw3
	0,	// slope 136 # nw2,se2
	0,	// slope 137 # nw2,se2,sw1
	0,	// slope 138 # nw2,se2,sw2
	0,	// slope 139 # nw2,se2,sw3
	0,	// slope 140 # nw2,se3
	0,	// slope 141 # nw2,se3,sw1
	0,	// slope 142 # nw2,se3,sw2
	0,	// slope 143 # nw2,se3,sw3
	0,	// slope 144 # nw2,ne1
	0,	// slope 145 # nw2,ne1,sw1
	0,	// slope 146 # nw2,ne1,sw2
	0,	// slope 147 # nw2,ne1,sw3
	0,	// slope 148 # nw2,ne1,se1
	0 | illegal,	// slope 149 # nw2,ne1,se1,sw1	TODO	64 up 1
	way_ew | illegal,	// slope 150 # nw2,ne1,se1,sw2	TODOew	65 up 1
	0 | illegal,	// slope 151 # nw2,ne1,se1,sw3	TODOew	66 up 1
	0,	// slope 152 # nw2,ne1,se2
	0 | illegal,	// slope 153 # nw2,ne1,se2,sw1	TODO	68 up 1
	0 | illegal,	// slope 154 # nw2,ne1,se2,sw2	TODO	69 up 1
	0 | illegal,	// slope 155 # nw2,ne1,se2,sw3	TODO	70 up 1
	0,	// slope 156 # nw2,ne1,se3
	0 | illegal,	// slope 157 # nw2,ne1,se3,sw1	TODO	72 up 1
	0 | illegal,	// slope 158 # nw2,ne1,se3,sw2	TODO	73 up 1
	0 | illegal,	// slope 159 # nw2,ne1,se3,sw3	TODO	74 up 1
	way_ns,	// slope 160 # nw2,ne2	straight ns2
	0,	// slope 161 # nw2,ne2,sw1
	0,	// slope 162 # nw2,ne2,sw2
	0,	// slope 163 # nw2,ne2,sw3
	0,	// slope 164 # nw2,ne2,se1
	way_ns | illegal,	// slope 165 # nw2,ne2,se1,sw1	TODOns	80 up 1
	0 | illegal,	// slope 166 # nw2,ne2,se1,sw2	TODO	81 up 1
	0 | illegal,	// slope 167 # nw2,ne2,se1,sw3	TODO	82 up 1
	0,	// slope 168 # nw2,ne2,se2
	0 | illegal,	// slope 169 # nw2,ne2,se2,sw1	TODO	84 up 1
	way_ns | way_ew | illegal,	// slope 170 # nw2,ne2,se2,sw2	TODO	[[85 up 1 =]] 0 up 2
	0 | illegal,	// slope 171 # nw2,ne2,se2,sw3	TODO	[[86 up 2 =]] 1 up 2
	0,	// slope 172 # nw2,ne2,se3
	0 | illegal,	// slope 173 # nw2,ne2,se3,sw1	TODO	88 up 1
	0 | illegal,	// slope 174 # nw2,ne2,se3,sw2	TODO	[[89 up 1 =]] 4 up 2
	way_ns | illegal,	// slope 175 # nw2,ne2,se3,sw3	TODO	[[90 up 1 =]] 5 up 2
	0,	// slope 176 # nw2,ne3
	0,	// slope 177 # nw2,ne3,sw1
	0,	// slope 178 # nw2,ne3,sw2
	0,	// slope 179 # nw2,ne3,sw3
	0,	// slope 180 # nw2,ne3,se1
	0 | illegal,	// slope 181 # nw2,ne3,se1,sw1	TODOns	96 up 1
	0 | illegal,	// slope 182 # nw2,ne3,se1,sw2	TODO	97 up 1
	0 | illegal,	// slope 183 # nw2,ne3,se1,sw3	TODO	98 up 1
	0,	// slope 184 # nw2,ne3,se2
	0 | illegal,	// slope 185 # nw2,ne3,se2,sw1	TODO	100 up 1
	0 | illegal,	// slope 186 # nw2,ne3,se2,sw2	TODO	101 up 1
	0 | illegal,	// slope 187 # nw2,ne3,se2,sw3	TODO	102 up 1
	0,	// slope 188 # nw2,ne3,se3
	0 | illegal,	// slope 189 # nw2,ne3,se3,sw1	TODO	104 up 1
	way_ew | illegal,	// slope 190 # nw2,ne3,se3,sw2	TODO	105 up 1 [[=20 up 2]]
	0 | illegal,	// slope 191 # nw2,ne3,se3,sw3	TODO	106 up 1 [[=21 up 2]
	0, // slope 192 # nw3,ne0,se0,sw0
	0, // slope 193 # nw3,ne0,se0,sw1
	0, // slope 194 # nw3,ne0,se0,sw2
	way_ew, // slope 195 # nw3,ne0,se0,sw3
	0, // slope 196 # nw3,ne0,se1,sw0
	0, // slope 197 # nw3,ne0,se1,sw1
	0, // slope 198 # nw3,ne0,se1,sw2
	0, // slope 199 # nw3,ne0,se1,sw3
	0, // slope 200 # nw3,ne0,se2,sw0
	0, // slope 201 # nw3,ne0,se2,sw1
	0, // slope 202 # nw3,ne0,se2,sw2
	0, // slope 203 # nw3,ne0,se2,sw3
	0, // slope 204 # nw3,ne0,se3,sw0
	0, // slope 205 # nw3,ne0,se3,sw1
	0, // slope 206 # nw3,ne0,se3,sw2
	0, // slope 207 # nw3,ne0,se3,sw3
	0, // slope 208 # nw3,ne1,se0,sw0
	0, // slope 209 # nw3,ne1,se0,sw1
	0, // slope 210 # nw3,ne1,se0,sw2
	0, // slope 211 # nw3,ne1,se0,sw3
	0, // slope 212 # nw3,ne1,se1,sw0
	0 | illegal, // slope 213 # nw3,ne1,se1,sw1
	0 | illegal, // slope 214 # nw3,ne1,se1,sw2
	way_ew | illegal, // slope 215 # nw3,ne1,se1,sw3
	0, // slope 216 # nw3,ne1,se2,sw0
	0 | illegal, // slope 217 # nw3,ne1,se2,sw1
	0 | illegal, // slope 218 # nw3,ne1,se2,sw2
	0 | illegal, // slope 219 # nw3,ne1,se2,sw3
	0, // slope 220 # nw3,ne1,se3,sw0
	0 | illegal, // slope 221 # nw3,ne1,se3,sw1
	0 | illegal, // slope 222 # nw3,ne1,se3,sw2
	0 | illegal, // slope 223 # nw3,ne1,se3,sw3
	0, // slope 224 # nw3,ne2,se0,sw0
	0, // slope 225 # nw3,ne2,se0,sw1
	0, // slope 226 # nw3,ne2,se0,sw2
	0, // slope 227 # nw3,ne2,se0,sw3
	0, // slope 228 # nw3,ne2,se1,sw0
	0 | illegal, // slope 229 # nw3,ne2,se1,sw1
	0 | illegal, // slope 230 # nw3,ne2,se1,sw2
	0 | illegal, // slope 231 # nw3,ne2,se1,sw3
	0, // slope 232 # nw3,ne3,se2,sw0
	0 | illegal, // slope 233 # nw3,ne2,se2,sw1
	0 | illegal, // slope 234 # nw3,ne2,se2,sw2
	way_ew | illegal, // slope 235 # nw3,ne2,se2,sw3
	0, // slope 236 # nw3,ne3,se3,sw0
	0 | illegal, // slope 237 # nw3,ne2,se3,sw1
	0 | illegal, // slope 238 # nw3,ne2,se3,sw2
	0 | illegal, // slope 239 # nw3,ne2,se3,sw3
	way_ns, // slope 240 # nw3,ne3,se0,sw0
	0, // slope 241 # nw3,ne3,se0,sw1
	0, // slope 242 # nw3,ne3,se0,sw2
	0, // slope 243 # nw3,ne3,se0,sw3
	0, // slope 244 # nw3,ne3,se1,sw0
	way_ns | illegal, // slope 245 # nw3,ne3,se1,sw1
	0 | illegal, // slope 246 # nw3,ne3,se1,sw2
	0 | illegal, // slope 247 # nw3,ne3,se1,sw3
	0, // slope 248 # nw3,ne3,se2,sw0
	0 | illegal, // slope 249 # nw3,ne3,se2,sw1
	way_ns | illegal, // slope 250 # nw3,ne3,se2,sw2
	0 | illegal, // slope 251 # nw3,ne3,se2,sw3
	0, // slope 252 # nw3,ne3,se3,sw0
	0 | illegal, // slope 253 # nw3,ne3,se3,sw1
	0 | illegal, // slope 254 # nw3,ne3,se3,sw2
	way_ns | way_ew | illegal, // slope 255 # nw3,ne3,se3,sw3
};


void DrawVerticalGray( PIXRGB *dest, int h, int dest_w, png_uint_32 grey )
{
	png_uint_32 color = (grey<<16) | (grey<<8) | (grey<<24l);
	while(  h-->0  ) {
		*dest = color;
		dest += dest_w;
	}
}


void DrawVerticalColor( PIXRGB *dest, int h, int dest_w, png_uint_32 color )
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
		png_uint_32 left_brigthnes = correct_color( base_brightness - (png_uint_32)( (brightness*( (nw-sw)*sun[0]*z_step + (se-sw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (nw-sw)*(nw-sw)*z_step*z_step + (se-sw)*(se-sw)*z_step*z_step + 1.0 ) ) ) );
		png_uint_32 right_brigthnes = correct_color( base_brightness - (png_uint_32)( (brightness*( (ne-se)*sun[0]*z_step + (ne-nw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (ne-se)*(ne-se)*z_step*z_step + (ne-nw)*(ne-nw)*z_step*z_step + 1.0 ) ) ) );

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
		png_uint_32 back_brigthnes = correct_color( base_brightness - (png_uint_32)( (brightness*( (nw-sw)*sun[0]*z_step + (ne-nw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (nw-sw)*(nw-sw)*z_step*z_step + (ne-nw)*(ne-nw)*z_step*z_step + 1.0 ) ) ) );
		png_uint_32 front_brigthnes = correct_color( base_brightness - (png_uint_32)( (brightness*( (ne-se)*sun[0]*z_step + (se-sw)*sun[1]*z_step - 1*sun[2] )) / ( sun_abs + sqrt( (ne-se)*(ne-se)*z_step*z_step + (se-sw)*(se-sw)*z_step*z_step + 1.0 ) ) ) );

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

	if(  slope >= 64  ) {
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
	if(  slope < 64  ) {
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
					if(  !strcmp(argv[i]+1,"c#")  ) {
						Usage(argv[0]);
						break;
					}
					default_color = strtol( argv[i]+3, NULL, 16 );
					default_color = (((default_color>>16)&0x000000FF)<<8)|(((default_color>>8)&0x000000FF)<<16)|((default_color&0x000000FF)<<24l);
					break;


				case 'p':
				case 'P':
					if(  !strcmp(argv[i]+1,"pak")  ) {
						Usage(argv[0]);
						break;
					}
					pak = atol( argv[i]+4 );
					break;
					
				case 's':
				case 'S':
					if(  !strcmp(argv[i]+1,"slope")  ) {
						Usage(argv[0]);
						break;
					}
					make_lightmap = true;
					slope_step = atol( argv[i]+6 );
					break;

				case 'm':
				case 'M':
					if(  !strcmp(argv[i]+1,"marker")  ) {
						Usage(argv[0]);
						break;
					}
					make_marker = true;
					slope_step = atol( argv[i]+7 );
					break;

				case 'b':
				case 'B':
					if(  !strcmp(argv[i]+1,"bright")  ) {
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
	const long corrected_pak = ((pak/2 + slope_step*3) > pak) ? (pak/2 + slope_step*3) : pak;
	const long row = make_lightmap ? 16 : 16;
	const long column = make_lightmap ? 16 : 8;
	const long bitmap_x = corrected_pak*row;
	const long bitmap_y = corrected_pak*column;
	PIXRGB *bitmap = (PIXRGB *)malloc(bitmap_x*bitmap_y*sizeof(PIXRGB));
	for(  int i=0;  i<=bitmap_x*bitmap_y;  i++  ) {
		bitmap[i] = TRANSPARENT;
	}

	if(  make_lightmap  ) {
		/* Create tile by tile */
		sun_abs = sqrt( sun[0]*sun[0] + sun[1]*sun[1] + sun[2]*sun[2] );
		base_brightness = 128 - (int)(brightness*sun[2]/(sun_abs+1.0));
		for(  i=0;  i<=255;  i++  ) {
			CreateSlope( i, bitmap + (corrected_pak-pak)*bitmap_x + corrected_pak*(i%row) + (i / row)*bitmap_x*corrected_pak, bitmap_x );
		}
	}
	else {
		for(  i=0;  i<128;  i++  ) {
			CreateMarker( i, bitmap + (corrected_pak-pak)*bitmap_x + corrected_pak*(i%row) + (i / row)*bitmap_x*corrected_pak, bitmap_x );
		}
	}

	/* write png */
	write_png( argv[last_option], (unsigned char *)bitmap, bitmap_x, bitmap_y, 32 );
	return 0;
}

