#ifdef __cplusplus
extern "C" {
#endif

void read_png(unsigned char **block, unsigned *width, unsigned *height, FILE *file);

void write_png( char *file_name, int w, int h, void *data );


#ifdef __cplusplus
}
#endif

