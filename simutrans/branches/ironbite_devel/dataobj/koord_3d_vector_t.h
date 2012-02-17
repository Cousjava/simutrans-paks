#ifndef KOORD3D_VECTOR_T_H
#define KOORD3D_VECTOR_T_H

#include "ribi.h"
#include "koord3d.h"
#include "../tpl/vector_tpl.h"

/**
 * This class defines a vector_tpl<koord3d> with some
 * helper functions
 * @author Gerd Wachsmuth
 */
class koord3d_vector_t : public vector_tpl< koord3d >
	{
public:
	/** computes ribi at position i */
	ribi_t::ribi get_ribi( uint32 index ) const;
	
	/** computes ribi at position i only if distance to previous/next is not larger than 1 */
	ribi_t::ribi get_short_ribi( uint32 index ) const;

	void rotate90( sint16 );
};

#endif
