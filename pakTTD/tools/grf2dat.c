#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "rdwr_png.h"

/*
 * Hajo: RGB 888 padded to 32 bit
 */
typedef unsigned long   PIXRGB;

/* special colors */
#define TRANSPARENT 0xE7FFFF

// number of special colors
#define SPECIAL_COLORS 10

// colortable of the PCX recently loaded
unsigned char LUT[768];

/*
 * Definition of special colors
 * @author Hj. Malthaner
 */
const PIXRGB rgbt_simu[SPECIAL_COLORS] =
{
  0x395E7C, // Player colors
  0x4C7191,
  0x4C7191,
  0x6084A7,
  0x7497BD,
  0x88ABD3,
  0x9CBEE9,
  0xB0D2FF,
  0xE7FFFF,	// transparent
  0xE7FFFF
};

const PIXRGB rgbt_ttd[SPECIAL_COLORS] =
{
  0x081858, // Player colors
  0x0C2468,
  0x14347C,
  0x1C448C,
  0x285CA4,
  0x3878BC,
  0x4898D8,
  0x64ACE0,
  0x0000FF,
  0xFFFFFF
};

PIXRGB block_getpix( unsigned char *block, long x, long y, long ttd_width)
{
	return ((block[y * ttd_width * 3 + x * 3] << 16) + 
		(block[y * ttd_width * 3 + x * 3 + 1] << 8) + 
		(block[y * ttd_width * 3 + x * 3 + 2]));
}

PIXRGB block_getpix_pcx( unsigned char *block, long x, long y, long ttd_width)
{
	unsigned char color = block[y * ttd_width + x];
	return block_getpix( LUT, color, 0, 0 );
}

void block_setpix( unsigned char *block, long x, long y, long ttd_width, PIXRGB color)
{
	block[y * ttd_width * 3 + x * 3]  = color>>16;
	block[y * ttd_width * 3 + x * 3 + 1] = color >> 8;
	block[y * ttd_width * 3 + x * 3 + 2] = color;
}

/* */
void convert_ttd_img_to_simutrans_img( PIXRGB *ttd_img, int ttd_width, int sx, int sy, int sw, int sh, PIXRGB *sim_img, int sim_width, int dx, int dy )
{
	int x, y;

printf("convert_ttd_img_to_simutrans_img(img,%i,%i,%i,%i,%i, dest,%i,%i,%i\n", ttd_width, sx, sy, sw, sh, sim_width, dx, dy );

	for( y=0;  y<=sh;  y++ )
	{
		for( x=0;  x<=sw;  x++ )
		{
#ifdef PNG_SRC
			PIXRGB	color = block_getpix( (unsigned char *)ttd_img, x+sx, y+sy, ttd_width );
#else
			PIXRGB	color = block_getpix_pcx( (unsigned char *)ttd_img, x+sx, y+sy, ttd_width );
#endif
			int i;
			for( i=0;  i<SPECIAL_COLORS  &&  color!=rgbt_ttd[i];  i++ )
				;
			// no special color => copy
			if(  i<SPECIAL_COLORS  )
				color = rgbt_simu[i];
			block_setpix( (unsigned char *)sim_img, x+dx, y+dy, sim_width, color );
		}
	}
}



// lower left corners for rail and everything else
int offsets_rail[16] = 
{
	28, 50,	// vertical
	28, 50,	// left down
	25, 50, 	// side
	20, 52,	// right down
	28, 50,	// vertical
	28, 50,	// left down
	25, 50, 	// side
	20, 52	// right down
};

// lower left corners for cars (because of roads with right side traffic shifted)
int offsets_road_old[16] = 
{
	34, 52,	// vertical up
	34, 52,	// right up
	25, 52, 	// side right
	14, 52,	// right down
	20, 48,	// vertical down
	22, 46,	// left down
	22, 46, 	// side left
	25, 44	// left up
};

// lower left corners for cars (because of roads with right side traffic shifted)
int offsets_road[16] = 
{
	34, 52,	// vertical up
	34, 50,	// right up
	25, 52, 	// side right
	14, 54,	// right down
	20, 48,	// vertical down
	24, 48,	// left down
	22, 46, 	// side left
	25, 50	// left up
};

// converts essentiall the images to simutrans images
void parse_nfo( FILE *f, int start, int stop, void *sim_img, int yoffset, int type )
{
	char str[1204];
	int i;

	static PIXRGB *ttd_img=NULL;
	static unsigned ttd_w, ttd_h;
	static char ttd_sprites[1024];

	int nr, x0, y0, dummy, h, w, xoff, yoff;
	int *offsets=(type&1)==0?offsets_rail:offsets_road;
	char new_ttd_sprites[1024], *img;
	int sim_width=(stop-start)*64;

	do
	{
		fgets( str, 1023, f );
		str[1024] = 0;
		i = atoi( str );
		if(i>=start  &&  i<stop)
		{
			int pos=0;
			// now decode them
			sscanf( str,  "%i %s %i %i %i %i %i %i %i", &nr, new_ttd_sprites, &x0, &y0, &dummy, &h, &w, &xoff, &yoff );
			if(dummy==0) {
				h = w;
				w = xoff;
			}
#ifdef PNG_SRC
			// need to reread the data?
			if(  strcmp(ttd_sprites,new_ttd_sprites)!=0  ) {
				FILE *fpng=fopen(new_ttd_sprites,"rb");
				if(fpng==NULL) {
					printf("\n**** Could not open png-file %s ****\n\n(aborting, press any key)\,",new_ttd_sprites);
					getchar();
					abort();
				}
				if(ttd_img!=NULL) {
					free(ttd_img);
				}
				read_png( &ttd_img, &ttd_w, &ttd_h, fpng );
				fclose(fpng);
				strcpy( ttd_sprites, new_ttd_sprites );
			}
#else
			// need to reread the data?
			if(  strcmp(ttd_sprites,new_ttd_sprites)!=0  ) {
				if(ttd_img!=NULL) {
					free(ttd_img);
				}
				read_pcx( new_ttd_sprites, &ttd_img, LUT, &ttd_w, &ttd_h );
printf("Read pcx (%i x %i) at %p\n",ttd_w,ttd_h,ttd_img);
				strcpy( ttd_sprites, new_ttd_sprites );
			}
#endif
			// which image
			pos = (nr-start);
			if(type<2) {
				convert_ttd_img_to_simutrans_img( ttd_img, ttd_w, x0, y0, w, h, sim_img, sim_width, pos*64+offsets[2*pos], yoffset+offsets[(2*pos)+1]-h );
			}
			else {
				// airplane/boat
				convert_ttd_img_to_simutrans_img( ttd_img, ttd_w, x0, y0, w, h, sim_img, sim_width, pos*64+32-w/2, yoffset+58-h );
			}
		}
	} while( !feof(f) &&  i<=stop  );
}



char *ttdx8_to_simutrans_view[8] =
{
	"NW", "N", "NE", "E", "SE", "S", "SW", "W"
};

char *ttdx4_to_simutrans_view[4] =
{
	"SE", "S", "SW", "W"
};



// parses the text file needed for each car
int parse_dgf_file(FILE *fIn)
{
	FILE *f=NULL;
	char **img_names;
	char name[256], str[1024], nfo[1024], native_data[4096];
	int type = 0;
	int freight = 0;
	int num_images = 0;
	int i = 0;
	int img_empty[2] = {0,0};
	int img_freight[2] = {0,0};
	PIXRGB *sim_img = NULL;

	nfo[0] = 0;
	name[0] = 0;
	native_data[0] = 0;

	while( !feof(fIn) )
	{
		fgets( str, 1024, fIn );
		if(str[0]=='-') {
			break;
		}
		if(strncmp("nfo=",str,4)==0) {
			strcpy( nfo, str+4 );
			for(i=0;  nfo[i]>32;  i++  )
				;
			nfo[i] = 0;
		} else if(strncmp("waytype",str,7)==0) {
			// check, if this is a car
			strcat( native_data, str );
			type = strstr(str,"road")!=NULL;
			type |= (strstr(str,"air")!=NULL)*2;
		} else if(strncmp("fi=",str,3)==0) {
			freight = 1;
			sscanf(str+3,"%i,%i", &(img_freight[0]), &(img_freight[1]) );
		} else if(strncmp("ei=",str,3)==0) {
			sscanf(str+3,"%i,%i", &(img_empty[0]), &(img_empty[1]) );
		} else if(strncmp("name=",str,5)==0) {
			strcat(native_data, str );
			strcpy( name, str+5 );
			// enforce sincle Line, no spaces, no special chars
			for(i=0;  name[i]>0;  i++  ) {
				if(name[i]==32) {
					name[i] = '-';
				}
				if(name[i]<32) {
					name[i] = 0;
					break;
				}
			}
		}
		else {
			strcat( native_data, str );
		}
	}
	if(nfo[0]==0  ||  name[0]==0) {
		return 0;
	}
	// sanity check
	if(  (img_freight[1]-img_freight[0])%4!=0  ||  (img_empty[1]-img_empty[0])%4!=0  ||  (img_empty[1]-img_empty[0])>8  ) {
		printf("ERROR in vehicle %s\nnumber of images out of range (must be either 4 or 8 images\n" );
		return 0;
	}
	// info
	printf("nfo:%s\nvehicle %s type %x, freight from %i,%i, empty from %i,%i\n",nfo,name, img_freight[0],img_freight[1],img_empty[0],img_empty[1] );
	// now get an trnasparent image
	num_images = img_empty[1]-img_empty[0];
	img_names = num_images>4 ? ttdx8_to_simutrans_view : ttdx4_to_simutrans_view;
	sim_img = malloc( 6*64*num_images*(1+freight)*64 );
	for( i=0;  i<64*64*num_images*(1+freight);  i++  ) {
		block_setpix( (unsigned char *)sim_img, i, 0, 0, 0xE7FFFF );
	}
	// now write the dat file
	sprintf( str, "%s.dat", name );
	f = fopen( str, "wb" );
	if(f==NULL) {
		printf("\n**** Could not open dat-file %s ****\n\n(aborting, press any key)\n",name);
		getchar();
		abort();
	}
	fprintf( f, "------------\nobj=vehicle\n" );
	fwrite( native_data, strlen(native_data), 1, f );
	for(  i=0;  i<num_images;  i++ ) {
		fprintf( f, "EmptyImage[%s]=%s.0.%i\n", img_names[i], name, i );
	}
	for(  i=0;  i<num_images  &&  freight;  i++ ) {
		fprintf( f, "FreightImage[%s]=%s.1.%i\n", img_names[i], name, i );
	}
	fputs( "----------------\n", f );
	fclose( f );
	// now the pictures
	f = fopen(nfo,"rb");
	if(f==NULL) {
		printf("\n**** Could not open nfo-file %s ****\n\n(aborting, press any key)\n",nfo);
		getchar();
		abort();
	}
	parse_nfo( f, img_empty[0], img_empty[1], sim_img, 0, type );
	if(freight) {
		parse_nfo( f, img_freight[0], img_freight[1], sim_img, 64, type );
	}
	fclose(f);
	strcat( name, ".png" );
	write_png( name, num_images*64, 64+freight*64, sim_img );
	free( sim_img );
	return 1;
}








void Usage(char *programName)
{
	fprintf(stderr,"%s usage:\n",programName);
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
					if (!stricmp(argv[i]+1,"help")) {
						Usage(argv[0]);
						break;
					}
					/* If the option -h means anything else
					 * in your application add code here
					 * Note: this falls through to the default
					 * to print an "unknow option" message
					*/
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
	if (argc == 1) {
		/* If no arguments we call the Usage routine and exit */
		Usage(argv[0]);
		return 1;
	}
	/* handle the program options */
	i = HandleOptions(argc,argv);
	/* The code of your application goes here */
	fIn = fopen( argv[i], "r" );
	while(parse_dgf_file(fIn)  &&  !feof(fIn))
		;
	fclose(fIn);
	return 0;
}

