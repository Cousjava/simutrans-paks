/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic license.
 */

#ifndef _MSC_VER
#include <unistd.h>
#include <sys/time.h>
#endif

#ifdef _WIN32
// windows.h defines min and max macros which we don't want
#define NOMINMAX 1
#include <windows.h>
#endif

#include "macros.h"
#include "simsys.h"


bool system_init(const int*)
{
	// prepare for next event
	sys_event.type = SIM_NOEVENT;
	sys_event.code = 0;
	return true;
}

resolution system_query_screen_resolution()
{
	resolution const res = { 0, 0 };
	return res;
}

// open the window
int system_open(int, int, int)
{
	return 1;
}

void system_close(void)
{
}

// reiszes screen
int dr_textur_resize(unsigned short** const textur, int, int)
{
	*textur = NULL;
	return 1;
}


unsigned short *system_init_framebuffer()
{
	return NULL;
}

unsigned int system_get_color(unsigned int, unsigned int, unsigned int)
{
	return 1;
}

// unused ?
void system_set_colors(int, int, unsigned char*)
{
}

void system_prepare_flush()
{
}

void system_flush_framebuffer(void)
{
}

void dr_textur(int, int, int, int)
{
}

void system_move_pointer(int, int)
{
}

void system_set_pointer(int)
{
}

int system_screenshot(const char *)
{
	return -1;
}

static inline unsigned int ModifierKeys(void)
{
	return 0;
}

void system_wait_event(void)
{
}

void system_poll_event(void)
{
}

void system_show_pointer(int)
{
}

void ex_ord_update_mx_my()
{
}

static timeval first;

unsigned long system_time(void)
{
	timeval second;
	gettimeofday(&second,NULL);
	if (first.tv_usec > second.tv_usec) {
		// since those are often unsigned
		second.tv_usec += 1000000;
		second.tv_sec--;
	}
	return (unsigned long)(second.tv_sec - first.tv_sec)*1000ul + (unsigned long)(unsigned long)(second.tv_usec - first.tv_usec)/1000ul;
}

void system_sleep(uint32 msec)
{
/*
	// this would be 100% POSIX but is usually not very accurate ...
	if(  msec>0  ) {
		struct timeval tv;
		tv.sec = 0;
		tv.usec = msec*1000;
		select(0, 0, 0, 0, &tv);
	}
*/
#ifdef _WIN32
	Sleep( msec );
#else
	sleep( msec );
#endif
}


int main(int argc, char **argv)
{
	gettimeofday(&first,NULL);
	return system_main(argc, argv);
}
