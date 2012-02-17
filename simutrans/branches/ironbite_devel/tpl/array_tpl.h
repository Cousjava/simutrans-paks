#ifndef TPL_ARRAY_TPL_H
#define TPL_ARRAY_TPL_H

#include <typeinfo>
#include "../simdebug.h"
#include "../simtypes.h"

/**
 * A template class for bounds checked 1-dimesnional arrays.
 * This is kept as simple as possible. Does not use exceptions
 * for error handling.
 */
template<class T> class array_tpl
{
	public:
		typedef const T* const_iterator;
		typedef       T* iterator;

		typedef uint32 index;

		explicit array_tpl() : data(NULL), size(0) {}

		explicit array_tpl(index s) : data(new T[s]), size(s) {}

		explicit array_tpl(index s, const T& value) : data(new T[s]), size(s)
		{
			for (index i = 0; i < size; i++) {
				data[i] = value;
			}
		}

		~array_tpl() { delete [] data; }

		index get_count() const { return size; }

		bool is_empty() const { return size == 0; }

		void clear()
		{
			delete [] data;
			data = 0;
			size = 0;
		}

		void resize(index resize)
		{
			if (size < resize) {
				T* new_data = new T[resize];
				for (index i = 0;  i < size; i++) {
					new_data[i] = data[i];
				}
				delete [] data;
				data = new_data;
				size = resize;
			}
		}

		void resize(index resize, const T& value)
		{
			if (size < resize) {
				T* new_data = new T[resize];
				index i;
				for (i = 0;  i < size; i++) {
					new_data[i] = data[i];
				}
				for (; i < resize; i++) {
					new_data[i] = value;
				}
				delete [] data;
				data = new_data;
				size = resize;
			}
		}

		const T & get(index i) const
		{
			if (i >= size) {
				dbg->fatal("array_tpl<T>::get", "index out of bounds: %d not in 0..%d, T=%s", i, size - 1, typeid(T).name());
			}
			return data[i];
		}

		T & at(index i) const
		{
			if (i >= size) {
				dbg->fatal("array_tpl<T>::at", "index out of bounds: %d not in 0..%d, T=%s", i, size - 1, typeid(T).name());
			}
			return data[i];
		}

		iterator begin() { return data; }
		iterator end()   { return data + size; }

		const_iterator begin() const { return data; }
		const_iterator end()   const { return data + size; }

	private:
		array_tpl(const array_tpl&);
		array_tpl& operator=( array_tpl const& other );

		T* data;
		index size;
};

/**
 * Iterator class for array templates.
 * Iterators may be invalid after any changing operation on the vector!
 *
 * This iterator can modify nodes, but not the list
 * Usage:
 *
 * array_iterator_tpl<T> iter(some_array);
 * while (iter.next()) {
 * 	T& current = iter.access_current();
 * }
 *
 * @author Hj. Malthaner
 */
template<class T> class array_iterator_tpl
{
private:
	const array_tpl<T> * const arr;
	int idx;

public:
	
	array_iterator_tpl(const array_tpl<T> * vector) : arr (vector)
	{
		idx = -1;
	}

	array_iterator_tpl(const array_tpl<T> & vector) : arr (&vector)
	{
		idx = -1;
	}

	array_iterator_tpl<T> &operator = (const array_iterator_tpl<T> &iter)
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
		return ((uint32)idx < arr->get_count());
	}

	
	/**
	 * @return the current element (as const reference)
	 * @author Hj. Malthaner
	 */
	const T & get_current() const
	{
		return arr->get((uint32)idx);
	}


	/**
	 * @return the current element (as reference)
	 * @author Hj. Malthaner
	 */
	T & access_current()
	{
		return arr->at((uint32)idx);
	}
};

#endif
