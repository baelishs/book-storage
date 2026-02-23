<?php

namespace Tests\Unit;

use App\DTO\Books\ImportBookDTO;
use App\Exceptions\Books\ExternalBookServiceException;
use App\Models\Book;
use App\Providers\Books\BookSearchStrategyResolver;
use App\Repositories\ExternalBookRepositoryInterface;
use App\Services\BookSearchService;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class BookSearchServiceTest extends TestCase
{
    private BookSearchStrategyResolver|MockObject $strategyResolverMock;
    private ExternalBookRepositoryInterface|MockObject $externalBookRepositoryMock;

    public function setUp(): void
    {
        $this->strategyResolverMock = $this->getMockBuilder(BookSearchStrategyResolver::class)->disableOriginalConstructor()->getMock();
        $this->externalBookRepositoryMock = $this->getMockBuilder(ExternalBookRepositoryInterface::class)->getMock();

        parent::setUp();
    }

    public function testImportBookWhenBookAlreadyExists()
    {
        $importBookDTO = new ImportBookDTO(
            userId: 1,
            externalId: 'ext-123',
            source: 'google',
            title: 'Test Book',
        );

        $this->externalBookRepositoryMock->expects($this->once())->method('existsByExternalId')->willReturn(true);
        $this->externalBookRepositoryMock->expects($this->never())->method('createFromImport');

        $service = new BookSearchService(
            strategyResolver: $this->strategyResolverMock,
            externalBookRepository: $this->externalBookRepositoryMock,
        );

        $this->expectException(ExternalBookServiceException::class);
        $this->expectExceptionCode(409);

        $service->importBook($importBookDTO);
    }

    public function testImportBookSuccessful()
    {
        $importBookDTO = new ImportBookDTO(
            userId: 1,
            externalId: 'ext-123',
            source: 'google',
            title: 'Test Book',
        );

        $mockBook = new Book();
        $mockBook->id = 1;
        $mockBook->user_id = 1;
        $mockBook->external_id = 'ext-123';
        $mockBook->title = 'Test Book';
        $mockBook->content = '';

        $this->externalBookRepositoryMock->expects($this->once())->method('existsByExternalId')
            ->with('ext-123')
            ->willReturn(false);
        $this->externalBookRepositoryMock->expects($this->once())->method('createFromImport')
            ->with($importBookDTO)
            ->willReturn($mockBook);

        $service = new BookSearchService(
            strategyResolver: $this->strategyResolverMock,
            externalBookRepository: $this->externalBookRepositoryMock,
        );

        $result = $service->importBook($importBookDTO);

        $this->assertSame($mockBook, $result);
        $this->assertSame('ext-123', $result->external_id);
        $this->assertSame('Test Book', $result->title);
    }
}
