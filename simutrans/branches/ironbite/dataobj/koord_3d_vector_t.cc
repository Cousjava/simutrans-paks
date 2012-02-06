#include "koord_3d_vector_t.h"

ribi_t::ribi koord3d_vector_t::get_ribi( uint32 index ) const
{
	ribi_t::ribi ribi = ribi_t::keine;
	koord3d pos = operator[](index);
	if( index > 0 ) {
		ribi |= ribi_typ( operator[](index-1).get_2d()-pos.get_2d() );
	}
	if( index+1 < get_count() ) {
		ribi |= ribi_typ( operator[](index+1).get_2d()-pos.get_2d() );
	}
	return ribi;
}

ribi_t::ribi koord3d_vector_t::get_short_ribi( uint32 index ) const
{
	ribi_t::ribi ribi = ribi_t::keine;
	const koord pos = operator[](index).get_2d();
	if( index > 0 ) {
		const koord pos2 = operator[](index-1).get_2d();
		if (koord_distance(pos,pos2)<=1) {
			ribi |= ribi_typ( pos2-pos );
		}
	}
	if( index+1 < get_count() ) {
		const koord pos2 = operator[](index+1).get_2d();
		if (koord_distance(pos,pos2)<=1) {
			ribi |= ribi_typ( pos2-pos );
		}
	}
	return ribi;
}

void koord3d_vector_t::rotate90( sint16 y_size )
{
	for( uint32 i = 0; i < get_count(); i++ ) {
		operator[](i).rotate90( y_size );
	}
}
