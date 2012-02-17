#ifndef TPL_VECTOR_H
#define TPL_VECTOR_H

#include <typeinfo>

#include "../macros.h"
#include "../simtypes.h"
#include "../simdebug.h"

template<class T> class vector_tpl;
template<class T> inline void swap(vector_tpl<T>& a, vector_tpl<T>& b);


/**
 * A template class for a simple vector type 
 *
 * @author Hj. Malthaner
 */
template<class T> class vector_tpl
{
	public:

		/** 
		 * Constructs a en empty vector 
		 * @author Hj. Malthaner
		 */
		vector_tpl() : data(NULL), capacity(0), count(0) 
		{
		}

		/** Construct a vector for cap elements */
		explicit vector_tpl(const uint32 cap) :
			data(cap > 0 ? new T[cap] : NULL),
			capacity(cap),
			count(0) 
		{
		}

		vector_tpl(const vector_tpl& copy_from) :
			data( copy_from.get_capacity() > 0 ? new T[ copy_from.get_capacity() ] : 0 ),
			capacity( copy_from.get_capacity() ),
			count( copy_from.get_count() ) 
		{
			for(uint32 i = 0; i < count; i++ )
			{
				data[i] = copy_from.data[i];
			}
		}

		~vector_tpl() 
		{ 
			delete [] data; 
			data = 0;
		}

		/** sets the vector to empty */
		void clear() 
		{
			count = 0; 
		}

		/**
		 * Resizes the maximum data that can be hold by this vector.
		 * Existing entries are preserved, new_size must be big enough to hold them
		 */
		void resize(const uint32 new_capacity)
		{
			if (new_capacity <= capacity) return; // do nothing

			T* new_data = new T[new_capacity];
			if(capacity>0) {
				for (uint32 i = 0; i < count; i++) {
					new_data[i] = data[i];
				}
				delete [] data;
			}
			capacity = new_capacity;
			data = new_data;
		}

		/**
		 * Checks if element elem is contained in vector.
		 * Uses the == operator for comparison.
		 */
		bool contains(const T & elem) const
		{
			for (uint32 i = 0; i < count; i++) {
				if (data[i] == elem) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Checks if element elem is contained in vector.
		 * Uses the == operator for comparison.
		 */
		uint32 index_of(const T & elem) const
		{
			for (uint32 i = 0; i < count; i++) {
				if (data[i] == elem) {
					return i;
				}
			}
			assert(false);
			return 0xFFFFFFFFu;
		}

		void append(const T & elem)
		{
			if(  count == capacity  ) {
				resize(capacity == 0 ? 1 : capacity * 2);
			}
			data[count++] = elem;
		}

		/**
		 * Checks if element is contained. Appends only new elements.
		 * extend vector if nessesary
		 */
		bool append_unique(const T & elem)
		{
			if (contains(elem)) 
			{
				return false;
			}
			append(elem);
			return true;
		}

		/** 
		 * Insert data at a certain pos 
		 */
		void insert_at(const uint32 pos, const T & elem)
		{
			if (pos < count) {
				if (count == capacity) {
					resize(capacity == 0 ? 1 : capacity * 2);
				}
				for (uint i = count; i > pos; i--) {
					data[i] = data[i - 1];
				}
				data[pos] = elem;
				count++;
			}
			else {
				append(elem);
			}
		}

		/**
		 * Insert `elem' with respect to ordering.
		 */
		template<class StrictWeakOrdering>
		void insert_ordered(const T& elem, StrictWeakOrdering comp)
		{
			sint32 low = -1, high = count;
			while(  high - low>1  ) {
				const sint32 mid = ((uint32) (low + high)) >> 1;
				T &mid_elem = data[mid];
				if(  comp(elem, mid_elem)  ) {
					high = mid;
				}
				else {
					low = mid;
				}
			}
			insert_at(high, elem);
		}

		/**
		 * Only insert `elem' if not already contained in this vector.
		 * Respects the ordering and assumes the vector is ordered.
		 * Returns NULL if insertion is successful;
		 * otherwise return the address of the element in conflict
		 */
		template<class StrictWeakOrdering>
		T* insert_unique_ordered(const T& elem, StrictWeakOrdering comp)
		{
			sint32 low = -1, high = count;
			while(  high - low>1  ) {
				const sint32 mid = ((uint32) (low + high)) >> 1;
				T &mid_elem = data[mid];
				if(  elem==mid_elem  ) {
					return &mid_elem;
				}
				else if(  comp(elem, mid_elem)  ) {
					high = mid;
				}
				else {
					low = mid;
				}
			}
			insert_at(high, elem);
			return NULL;
		}

		/**
		 * put the data at a certain position
		 * BEWARE: using this function will create default objects, depending on
		 * the type of the vector
		 */
		void store_at(const uint32 pos, const T & elem)
		{
			if (pos >= capacity) {
				resize((pos & 0xFFFFFFF7) + 8);
			}
			data[pos] = elem;
			if (pos >= count) {
				count = pos + 1;
			}
		}

		/** 
		 * Removes element, if contained 
		 */
		void remove(const T& elem)
		{
			for (uint32 i = 0; i < count; i++) {
				if (data[i] == elem) {
					return remove_at(i);
				}
			}
		}

		/** 
		 * Removes element at position 
		 */
		void remove_at(const uint32 pos)
		{
			assert(pos<count);
			for (uint32 i = pos; i < count - 1; i++) 
			{
				data[i] = data[i + 1];
			}
			count--;
		}

		T & at(const uint32 pos) const
		{
			if (pos >= count) 
			{
				dbg->fatal("vector_tpl<T>::at", "%s: index out of bounds: %i not in 0..%d", typeid(T).name(), pos, count - 1);
			}
			return data[pos];
		}

		const T & get(const uint32 pos) const
		{
			if (pos >= count) 
			{
				dbg->fatal("vector_tpl<T>::get", "%s: index out of bounds: %i not in 0..%d", typeid(T).name(), pos, count - 1);
			}
			return data[pos];
		}
		
		T & back() const { return data[count - 1]; }

		/** 
		 * Get the number of elements in the vector 
		 */
		uint32 get_count() const 
		{
			return count; 
		}

		/** 
		 * Get the capacity 
		 */
		uint32 get_capacity() const 
		{
			return capacity; 
		}

		/**
		 * @return true if this container is empty, false otherwise
		 * @author Hj. Malthaner
		 */
		bool is_empty() const 
		{ 
			return count == 0; 
		}

		/**
		 * Old C style sort call.
		 * @author Hj. Malthaner
		 */
		void sort(int (* comp)(const void * a, const void * b))
		{
			qsort(data, count, sizeof(T *), comp);
		}
		
	private:
		T* data;
		uint32 capacity;  ///< Capacity
		uint32 count; ///< Number of elements in vector

		vector_tpl& operator=( vector_tpl const& other ) {
			vector_tpl tmp(other);
			swap(tmp, *this);
			return *this;
		}

	friend void swap<>(vector_tpl<T>& a, vector_tpl<T>& b);
};


template<class T> void swap(vector_tpl<T>& a, vector_tpl<T>& b)
{
	sim::swap(a.data,  b.data);
	sim::swap(a.capacity,  b.capacity);
	sim::swap(a.count, b.count);
}

/**
 * Clears vectors of the type vector_tpl<someclass*>
 * Deletes all objects pointed to by pointers in the vector
 */
template<class T> void clear_ptr_vector(vector_tpl<T *> & v)
{
	const uint32 count = v.get_count();
	for(uint32 i=0; i<count; i++) 
	{
		delete v.at(i);
		v.at(i) = 0;
	}
	v.clear();
}

/**
 * Iterator class for vectors.
 * Iterators may be invalid after any changing operation on the vector!
 *
 * This iterator can modify nodes, but not the list
 * Usage:
 *
 * vector_iterator_tpl<T> iter(some_vector);
 * while (iter.next()) {
 * 	T& current = iter.access_current();
 * }
 *
 * @author Hj. Malthaner
 */
template<class T> class vector_iterator_tpl
{
private:
	const vector_tpl<T> * const vec;
	int idx;

public:
	
	vector_iterator_tpl(const vector_tpl<T> * vector) : vec (vector)
	{
		idx = -1;
	}

	vector_iterator_tpl(const vector_tpl<T> & vector) : vec (&vector)
	{
		idx = -1;
	}

	vector_iterator_tpl<T> &operator = (const vector_iterator_tpl<T> &iter)
	{
		idx = iter.idx;
		return *this;
	}

	/**
	 * iterate next element
	 * @return false, if no more elements
	 * @author Hj. Malthaner
	 */
	bool next()
	{
		idx++;
		return ((uint32)idx < vec->get_count());
	}

	
	/**
	 * @return the current element (as const reference)
	 * @author Hj. Malthaner
	 */
	const T & get_current() const
	{
		return vec->get((uint32)idx);
	}


	/**
	 * @return the current element (as reference)
	 * @author Hj. Malthaner
	 */
	T & access_current()
	{
		return vec->at((uint32)idx);
	}
};

#endif
