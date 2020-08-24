using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Text;
using TeamChat.Utilities;
using Xunit;

namespace TeamChat.BL.Tests
{
    public class CollectionComparerTests
    {
        [Fact]
        public void CollectionsEqual()
        {
            var collection1 = new Collection<int>();
            collection1.Add(1);
            collection1.Add(5);
            collection1.Add(17);

            var collection2 = new Collection<int>();
            collection2.Add(1);
            collection2.Add(5);
            collection2.Add(17);

            var comparer = new CollectionComparer<int>();
            Assert.True(comparer.Equals(collection1, collection2));
        }

        [Fact]
        public void CollectionsNotEqual()
        {
            var collection1 = new Collection<int>();
            collection1.Add(1);
            collection1.Add(5);
            collection1.Add(17);

            var collection2 = new Collection<int>();
            collection2.Add(1);
            collection2.Add(58);
            collection2.Add(17);

            var comparer = new CollectionComparer<int>();
            Assert.False(comparer.Equals(collection1, collection2));

            collection1.RemoveAt(1);

            Assert.False(comparer.Equals(collection1, collection2));
        }

    }
}
