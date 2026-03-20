<?php
session_start();
include 'db_connect.php'; 
include 'header.php';     

while ($conn->more_results()) 
{
    $conn->next_result();
}

$cat_sql = "SELECT CategoryID, CategoryDescription FROM category";
$cat_res = $conn->query($cat_sql);
$categories = [];
if ($cat_res && $cat_res->num_rows > 0)
{
    while ($row = $cat_res->fetch_assoc())
    {
        $categories[] = $row;
    }
}

$limit = 5;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$term = isset($_POST['search_term']) ? $conn->real_escape_string($_POST['search_term']) : '';
$cat_filter = isset($_POST['category']) ? $_POST['category'] : 'all';
$where_clauses = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['search_term']) || isset($_GET['page'])) 
{
    if (!empty($term)) 
    {
        $where_clauses[] = "(b.BookTitle LIKE '%$term%' OR b.Author LIKE '%$term%')";
    }
    if ($cat_filter != 'all' && $cat_filter != '')
    {
        $where_clauses[] = "b.Category = '$cat_filter'";
    }
}

$where = "";
if (count($where_clauses) > 0)
{
    $where = " WHERE " . implode(" AND ", $where_clauses);
}

$count_sql = "SELECT COUNT(b.ISBN) FROM book b LEFT JOIN category c ON b.Category = c.CategoryID" . $where;
$count_res = $conn->query($count_sql);
$total_records = 0;
if ($count_res) 
{
    $total_records = $count_res->fetch_row()[0];
}
$total_pages = ceil($total_records / $limit);

$sql = "SELECT b.*, c.CategoryDescription FROM book b LEFT JOIN category c ON b.Category = c.CategoryID" . $where . " ORDER BY b.BookTitle ASC LIMIT $start, $limit";

$res = $conn->query($sql);

if (!$res) 
{
    echo "<h3 style='color:red;'>SQL Error: " . $conn->error . "</h3>";
    echo "<p>Query: $sql</p>";
}
?>

<h2>Search for a Book</h2>

<form method="post" action="search.php">
    <div style="margin-bottom: 15px;">
        <label for="search_term">Title or Author:</label>
        <input type="text" id="search_term" name="search_term" value="<?php echo htmlspecialchars($term); ?>">
    </div>
    <div style="margin-bottom: 15px;">
        <label>Genre:</label>
        <select name="category">
            <option value="all">All Genres</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['CategoryID']; ?>" <?php if ($cat_filter == $cat['CategoryID']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat['CategoryDescription']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <input type="submit" value="Search">
</form>

<hr>

<h3>Results (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)</h3>
<?php
if ($res && $res->num_rows > 0) 
    { 
    echo "<table border='1' cellpadding='5' style='width:100%; border-collapse: collapse;'>";
    echo "<tr>
            <th>ISBN</th>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Status</th>
            <th>Action</th>
          </tr>";
    
    while ($row = $res->fetch_assoc()) 
        {
        
        $flag = isset($row['Reserve']) ? $row['Reserve'] : 'N';
        
        $clean_flag = trim(strtoupper($flag));

        $isReserved = ($clean_flag === 'Y');
        $status = $isReserved ? 'Reserved' : 'Available';
        
        $action = $isReserved ? 
                  '<span style="color:red; font-weight:bold;">Reserved</span>' : 
                  "<a href='reserve.php?isbn=" . $row['ISBN'] . "'>Reserve</a>"; 

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ISBN']) . "</td>";
        echo "<td>" . htmlspecialchars($row['BookTitle']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Author']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CategoryDescription']) . "</td>";
        echo "<td>" . $status . "</td>";
        echo "<td>" . $action . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $query_params = http_build_query(['search_term' => $term, 'category' => $cat_filter]);
    
    echo "<div style='text-align: center; margin-top: 20px;'>";
    if ($page > 1) 
    {
        echo "<a href='search.php?page=" . ($page - 1) . "&" . $query_params . "' style='margin-right: 10px;'>Previous</a>";
    }

    for ($i = 1; $i <= $total_pages; $i++) 
    {
        if ($i == $page) 
        {
            echo "<span style='font-weight: bold; margin: 0 5px; color: #007bff;'>" . $i . "</span>";
        } else 
        {
            echo "<a href='search.php?page=" . $i . "&" . $query_params . "' style='margin: 0 5px;'>" . $i . "</a>";
        }
    }

    if ($page < $total_pages) 
    {
        echo "<a href='search.php?page=" . ($page + 1) . "&" . $query_params . "' style='margin-left: 10px;'>Next</a>";
    }
    echo "</div>";

} else 
{
    echo "<p>No books found.</p>";
}

$conn->close();
include 'footer.php';
?>