/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 * Copyright (c) 2012 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic license.
 * (see license.txt)
 */

#ifndef tpl_slist_tpl_h
#define tpl_slist_tpl_h

#include <typeinfo>
#include "../dataobj/freelist.h"
#include "../simdebug.h"

#ifdef _MSC_VER
#pragma warning(disable:4786)
#endif

template <class T> class slist_iterator_tpl;

/**
 * A template class for a single linked list. Insert() and append()
 * work in fixed time. Maintains a list of free nodes to reduce calls
 * to new and delete.
 *
 * Must NOT be used with things with copy contructor like button_t or std::string!!!
 *
 * @date November 2000
 * @author Hj. Malthaner
 */

template<class T> class slist_tpl
{
	friend class slist_iterator_tpl<T>;
	
private:
	struct node_t
	{
		node_t(const T& data_, node_t* next_) : next(next_), data(data_) {}
		node_t(node_t* next_) : next(next_), data() {}

		void* operator new(size_t) { return freelist_t::gimme_node(sizeof(node_t)); }
		void operator delete(void* p) { freelist_t::putback_node(sizeof(node_t), p); }

		node_t* next;
		T data;
	};

	node_t *head;
	node_t *tail;
	uint32 node_count;

public:

	/**
	 * Creates a new empty list.
	 *
	 * @author Hj. Malthaner
	 */
	slist_tpl()
	{
		head = 0;             // leere liste
		tail = 0;
		node_count = 0;
	}

	~slist_tpl()
	{
		clear();
	}

	/**
	 * Inserts an element at the beginning of the list.
	 *
	 * @param data the element to insert
	 * @author Hj. Malthaner
	 */
	void insert(const T & data)
	{
		node_t* tmp = new node_t(data, head);
		head = tmp;
		if(tail == 0) 
		{
			tail = tmp;
		}
		node_count++;
	}

	/**
	 * Inserts an element at a given position.
	 *
	 * @param pos the position where to insert.
	 * @param data the element to insert.
	 *
	 * @author Hj. Malthaner
	 */
	void insert_at(uint32 pos, const T & data)
	{
		if(pos == 0)
		{
			insert(data);
			return;
		}
		if(pos >= node_count) 
		{
			append(data);
			return;
		}
		
		node_t* p = head;
		while(--pos) 
		{
			p = p->next;
		}

		node_t* tmp = new node_t(data, p->next);

		p->next = tmp;
		
		node_count++;
	}

	/**
	 * Inserts an element initialized by standard constructor
	 * at the beginning of the lists
	 * (avoid the use of copy constructor)
	 */
	void insert()
	{
		node_t* tmp = new node_t(head);
		head = tmp;
		if(tail == 0) 
		{
			tail = tmp;
		}
		node_count++;
	}

	/**
	 * Appends an element to the end of the list.
	 *
	 * @param data the element to append
	 * @author Hj. Malthaner
	 */
	void append(const T & data)
	{
		if(tail == 0) 
		{
			insert(data);
		}
		else 
		{
			node_t* tmp = new node_t(data, 0);
			tail->next = tmp;
			tail = tmp;
			node_count++;
		}
	}

	/**
	 * Append an zero/empty element
	 * mostly used for T=slist_tpl<...>
	 */
	void append()
	{
		if(tail == 0) 
		{
			insert();
		}
		else 
		{
			node_t* tmp = new node_t(0);
			tail->next = tmp;
			tail = tmp;
			node_count++;
		}
	}

	/**
	 * Appends an element to the end of the list,
	 * if this element was not yet in the list.
	 *
	 * @author Hj. Malthaner
	 */
	void append_unique(const T & data)
	{
		if(tail == 0) 
		{
			insert(data);
		}
		else if(!contains(data)) 
		{
			node_t* tmp = new node_t(data, 0);
			tail->next = tmp;
			tail = tmp;
			node_count++;
		}
	}

	/**
	 * Appends the nodes of another list
	 * empties other list
	 * -> no memory allocation involved
	 *
	 * @author dwachs
	 */
	void append_list(slist_tpl<T> & other)
	{
		if(tail) 
		{
			tail->next = other.head;
		}
		else 
		{
			head = other.head;
		}
		tail = other.tail;
		node_count += other.node_count;

		// empty other list
		other.tail = 0;
		other.head = 0;
		other.node_count = 0;
	}

	/**
	 * Checks if the given element is already contained in the list.
	 *
	 * @return true if found, false otherwise.
	 * @param data the element to check.
	 *
	 * @author Hj. Malthaner
	 */
	bool contains(const T & data) const
	{
		node_t * p = head;

		while(p != 0 && p->data != data) 
		{
			p = p->next;
		}
		return p != 0;
	}

	/**
	 * Removes an element from the list.
	 *
	 * @param data the element to remove
	 * @return true if data was in list, false otherwise
	 *
	 * @author Hj. Malthaner
	 */
	bool remove(const T & data)
	{
		if (is_empty()) 
		{
			return false;
		}

		if(head->data == data) 
		{
			node_t *tmp = head->next;
			delete head;
			head = tmp;

			if(head == NULL) 
			{
				tail = NULL;
			}
		}
		else 
		{
			node_t *p = head;

			while(p->next != 0 && !(p->next->data == data)) 
			{
				p = p->next;
			}
			if(p->next == 0) 
			{
				return false;
			}
			node_t *tmp = p->next->next;
			delete p->next;
			p->next = tmp;

			if(tmp == 0) 
			{
				tail = p;
			}
		}
		node_count--;
		
		return true;
	}

	/**
	 * Retrieves the first element from the list. This element is
	 * deleted from the list. Useful for some queueing tasks.
	 * @author Hj. Malthaner
	 */
	T remove_first()
	{
		if(is_empty()) 
		{
			dbg->fatal("slist_tpl<T>::remove_first()","List of <%s> is empty",typeid(T).name());
		}

		T tmp = head->data;
		node_t *p = head;

		head = head->next;
		delete p;

		node_count--;

		if(head == 0) 
		{
			// list is empty now
			tail = 0;
		}

		return tmp;
	}

	/**
	 * Recycles all nodes.
	 * Leaves the list empty.
	 *
	 * @author Hj. Malthaner
	 */
	void clear()
	{
		node_t* p = head;
		while(p != 0) 
		{
			node_t* tmp = p;
			p = p->next;
			delete tmp;
		}
		head = 0;
		tail = 0;
		node_count = 0;
	}

	/**
	 * Gets the number of data elements in this list.
	 * 
	 * @return the number of data elements
	 * @author Hj. Malthaner
	 */
	uint32  get_count() const
	{
		return node_count;
	}

	/**
	 * Check if this list is empty.
	 *
	 * @return true if this container is empty, false otherwise
	 * @author Hj. Malthaner
	 */
	bool is_empty() const 
	{
		return head == 0; 
	}

	T& at(uint32 pos) const
	{
		if (pos >= node_count) {
			dbg->fatal("slist_tpl<T>::at()", "<%s> index %d is out of bounds", typeid(T).name(), pos);
		}

		node_t* p = head;
		while (pos--) {
			p = p->next;
		}
		return p->data;
	}

	T & front() const { return head->data; }
	T & back()  const { return tail->data; }

	int index_of(T data) const
	{
		node_t *t = head;
		int index = 0;

		while(t && t->data != data) {
			t = t->next;
			index++;
		}
		return t ? index : -1;
	}

private:
	slist_tpl(const slist_tpl& slist_tpl);
	slist_tpl& operator=( slist_tpl const& other );

};


/**
 * Iterator class for single linked lists.
 * Iterators may be invalid after any changing operation on the list!
 *
 * This iterator can modify nodes, but not the list
 * Usage:
 *
 * slist_iterator_tpl<T> iter(some_list);
 * while (iter.next()) {
 * 	T& current = iter.access_current();
 * }
 *
 * @author Hj. Malthaner
 */
template<class T> class slist_iterator_tpl
{
private:
	typename slist_tpl<T>::node_t * current_node;
	typename slist_tpl<T>::node_t * next_node;

public:
	
	slist_iterator_tpl(const slist_tpl<T> * list) :
		// we start with NULL
		// after one call to next() current_node points to first node in list
		current_node(0),
		next_node(list->head)
	{
	}

	slist_iterator_tpl(const slist_tpl<T> & list) :
		current_node(0),
		next_node(list.head)
	{
	}

	slist_iterator_tpl<T> &operator = (const slist_iterator_tpl<T> &iter)
	{
		current_node = iter.current_node;
		next_node    = iter.next_node;
		return *this;
	}

	/**
	 * iterate next element
	 * @return false, if no more elements
	 * @author Hj. Malthaner
	 */
	bool next()
	{
		current_node = next_node;
		if (next_node) 
		{
			next_node = next_node->next;
		}
		return (current_node != 0);
	}

	
	/**
	 * @return the current element (as const reference)
	 * @author Hj. Malthaner
	 */
	const T & get_current() const
	{
		if(current_node == 0) 
		{
			dbg->fatal("slist_iterator_tpl.get_current()", 
				    "<%s> iteration accessed NULL!", 
				    typeid(T).name());
		}
		return current_node->data;
	}


	/**
	 * @return the current element (as reference)
	 * @author Hj. Malthaner
	 */
	T & access_current()
	{
		if(current_node == 0) 
		{
			dbg->fatal("slist_iterator_tpl.access_current()",
				    "<%s> iteration accessed NULL!", 
				    typeid(T).name());
		}
		return current_node->data;
	}
};

#endif
