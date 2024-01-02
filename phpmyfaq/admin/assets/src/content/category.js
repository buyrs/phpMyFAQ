/**
 * Category administration stuff
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2014-2024 phpMyFAQ Team
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2014-06-02
 */

import Sortable from 'sortablejs';
import { deleteCategory, setCategoryTree } from '../api';
import { addElement } from '../../../../assets/src/utils';

const nestedQuery = '.nested-sortable';
const identifier = 'pmfCatid';

export const handleCategories = () => {
  const root = document.getElementById('pmf-category-tree');
  const nestedSortables = document.querySelectorAll(nestedQuery);

  for (let i = 0; i < nestedSortables.length; i++) {
    new Sortable(nestedSortables[i], {
      group: 'Categories',
      animation: 150,
      fallbackOnBody: true,
      swapThreshold: 0.65,
      dataIdAttr: identifier,
      store: {
        set: async () => {
          const csrf = document.querySelector('input[name=pmf-csrf-token]').value;
          const data = serializedTree(root);
          const response = await setCategoryTree(data, csrf);
          handleAlert(response);
        },
      },
    });
  }

  const serializedTree = (sortable) => {
    return Array.from(sortable.children).map((child) => {
      const nested = child.querySelector(nestedQuery);
      return {
        id: child.dataset[identifier],
        children: nested ? serializedTree(nested) : [],
      };
    });
  };
};

export const handleCategoryDelete = async () => {
  const buttonDelete = document.getElementsByName('pmf-category-delete-button');

  if (buttonDelete) {
    buttonDelete.forEach((button) => {
      button.addEventListener('click', async (event) => {
        event.preventDefault();
        const categoryId = event.target.getAttribute('data-pmf-category-id');
        const language = event.target.getAttribute('data-pmf-language');
        const csrfToken = document.querySelector('input[name=pmf-csrf-token]').value;

        const response = await deleteCategory(categoryId, language, csrfToken);
        handleAlert(response);
        document.getElementById(`pmf-category-${categoryId}`).remove();
      });
    });
  }
};

const handleAlert = (response) => {
  const result = document.getElementById('pmf-category-result');
  if (response.success) {
    result.append(
      addElement(
        'div',
        {
          classList: 'alert alert-success alert-dismissible fade show',
          innerText: response.success,
          role: 'alert',
        },
        [
          addElement('button', {
            classList: 'btn-close',
            type: 'button',
            'data-bsDismiss': 'alert',
            'aria-label': 'Close',
          }),
        ]
      )
    );
  }
};
