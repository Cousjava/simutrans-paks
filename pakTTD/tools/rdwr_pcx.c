#include <stdio.h>


void read_pcx(char *fname,unsigned char **data, unsigned char *LUT, unsigned *w, unsigned *h)
{
	unsigned char PCXHeader[128];
	unsigned char Pixel;
	unsigned char Anzahl;
	FILE *PCX_fd=fopen(fname,"rb");                     /* File-Descriptor */
	int i, j, z, s;
	long Index;

	unsigned int bildzeilen;
	unsigned int bildspalten;
	unsigned char *bild;             /* Adresse des Bildspeichers (Array) */

	*data = NULL;

	if(PCX_fd==NULL) {
		printf("\n**** Could not open pcx-file %s ****\n\n(aborting, press any key)\n",fname);
		getchar();
		abort();
	}

	/* PCX-Header einlesen und auf Version 5 testen */
	fread(PCXHeader, 128,1,PCX_fd);
	if((PCXHeader[0]!=10) || (PCXHeader[1]!=5) || (PCXHeader[2]!=1) || (PCXHeader[3]!=8))
	{
		printf("PCX image error!\n");
		abort();
	}
	
	/* Bildmaﬂe berechnen (this works on all endian machines) */
	/* assuming offset is zero */
	bildspalten = 256*(unsigned int)PCXHeader[9]+(unsigned int)PCXHeader[8]+1;
	bildzeilen = 256*(unsigned int)PCXHeader[11]+(unsigned int)PCXHeader[10]+1;

	bild = (unsigned char *)malloc( bildspalten*bildzeilen );

	/* RLC einlesen */
	Index = 0;
	z = 0;
	while(z<bildzeilen)
	{
		s = 0;
		j = 0;
		while(s<bildspalten)
		{
			fread(&Pixel, 1,1,PCX_fd);
			if(Pixel>192)
			{
				Anzahl = Pixel-192;
				fread(&Pixel, 1,1,PCX_fd);
				for(i=0; i<Anzahl; i++)
				{
					bild[Index++] = Pixel;
					s++;
				}
			}
			else
			{
				bild[Index++] = Pixel;
				s++;
			}
		}
		z++;
	}
	
	/* Einlesen der Farbpalette */
	fread(&Pixel,1,1,PCX_fd);
	if(Pixel!=12) {
		printf("Wrong palette header (12 expected, found %i)",Pixel);
	}
	fread(LUT,768,1,PCX_fd);

	/* set return values */
	*data = bild;
	*w = bildspalten;
	*h = bildzeilen;

	fclose(PCX_fd);
}
